import { ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useWebSocket(channels = []) {
  const ws = ref(null)
  const isConnected = ref(false)
  const reconnectAttempts = ref(0)
  const maxReconnectAttempts = 5
  const reconnectDelay = 3000 // 3 seconds
  let reconnectTimeout = null

  const page = usePage()
  const userId = page.props.auth?.user?.id || 'guest'
  const userName = page.props.auth?.user?.name || 'Guest'

  const connect = () => {
    try {
      const wsUrl = `ws://localhost:6001?user_id=${userId}&user_name=${encodeURIComponent(userName)}`
      ws.value = new WebSocket(wsUrl)

      ws.value.onopen = () => {
        console.log('✅ WebSocket connected')
        isConnected.value = true
        reconnectAttempts.value = 0

        // Subscribe to channels
        if (channels.length > 0) {
          subscribe(channels)
        }
      }

      ws.value.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)
          // Skip ping/pong messages
          if (data.type === 'ping' || data.status === 'pong') {
            return
          }
          handleMessage(data)
        } catch (error) {
          console.error('Error parsing WebSocket message:', error)
        }
      }

      ws.value.onerror = (error) => {
        console.error('❌ WebSocket error:', error)
        isConnected.value = false
      }

      ws.value.onclose = () => {
        console.log('🔌 WebSocket disconnected')
        isConnected.value = false
        attemptReconnect()
      }
    } catch (error) {
      console.error('Error connecting to WebSocket:', error)
      attemptReconnect()
    }
  }

  const subscribe = (channelList) => {
    if (!ws.value || ws.value.readyState !== WebSocket.OPEN) {
      console.warn('WebSocket not connected, cannot subscribe')
      return
    }

    const channelsToSubscribe = Array.isArray(channelList) ? channelList : [channelList]
    
    ws.value.send(JSON.stringify({
      action: 'subscribe',
      channels: channelsToSubscribe,
      user_id: userId,
      user_name: userName
    }))

    console.log(`📡 Subscribed to channels:`, channelsToSubscribe)
  }

  const unsubscribe = (channelList) => {
    if (!ws.value || ws.value.readyState !== WebSocket.OPEN) {
      return
    }

    const channelsToUnsubscribe = Array.isArray(channelList) ? channelList : [channelList]
    
    ws.value.send(JSON.stringify({
      action: 'unsubscribe',
      channels: channelsToUnsubscribe
    }))

    console.log(`📡 Unsubscribed from channels:`, channelsToUnsubscribe)
  }

  const attemptReconnect = () => {
    if (reconnectAttempts.value >= maxReconnectAttempts) {
      console.error('Max reconnection attempts reached')
      return
    }

    reconnectAttempts.value++
    console.log(`🔄 Attempting to reconnect (${reconnectAttempts.value}/${maxReconnectAttempts})...`)

    reconnectTimeout = setTimeout(() => {
      connect()
    }, reconnectDelay)
  }

  const disconnect = () => {
    if (reconnectTimeout) {
      clearTimeout(reconnectTimeout)
      reconnectTimeout = null
    }

    if (ws.value) {
      // Unsubscribe from all channels before closing
      if (channels.length > 0) {
        unsubscribe(channels)
      }
      
      ws.value.close()
      ws.value = null
    }
    
    isConnected.value = false
  }

  // Default message handler - can be overridden
  let messageHandler = (data) => {
    console.log('WebSocket message received:', data)
  }
  
  const setMessageHandler = (handler) => {
    messageHandler = handler
  }
  
  const handleMessage = (data) => {
    messageHandler(data)
  }

  onMounted(() => {
    connect()
  })

  onUnmounted(() => {
    disconnect()
  })

  return {
    ws,
    isConnected,
    connect,
    disconnect,
    subscribe,
    unsubscribe,
    handleMessage,
    setMessageHandler
  }
}

