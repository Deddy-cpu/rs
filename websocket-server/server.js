import { WebSocketServer } from 'ws';
import express from 'express';
import cors from 'cors';

const app = express();
const port = 6001;

// REST endpoint untuk menerima notifikasi dari Laravel
app.use(cors());
app.use(express.json());

const server = app.listen(port, () => {
  console.log(`WebSocket Server running on port ${port}`);
});

const wss = new WebSocketServer({ server });

// Store client info: { ws: { userId, userName, subscribedChannels: Set } }
const clientInfo = new Map();

// Helper function to broadcast to all clients
function broadcastToAll(message) {
  const messageStr = JSON.stringify(message);
  wss.clients.forEach((client) => {
    if (client.readyState === 1) { // 1 = OPEN
      client.send(messageStr);
    }
  });
}

// Helper function to broadcast to specific channel subscribers
function broadcastToChannel(channel, message) {
  const messageStr = JSON.stringify({
    ...message,
    channel: channel
  });
  
  let sentCount = 0;
  wss.clients.forEach((client) => {
    if (client.readyState === 1) { // 1 = OPEN
      const info = clientInfo.get(client);
      if (info && info.subscribedChannels && info.subscribedChannels.has(channel)) {
        client.send(messageStr);
        sentCount++;
      }
    }
  });
  
  console.log(`Broadcasted to ${sentCount} clients on channel: ${channel}`);
}

// Helper function to send to specific client
function sendToClient(ws, message) {
  if (ws.readyState === 1) { // 1 = OPEN
    ws.send(JSON.stringify(message));
  }
}

// Clean up when client disconnects
function cleanupClient(ws) {
  clientInfo.delete(ws);
}

// REST endpoint untuk menerima notifikasi dari Laravel
app.post('/broadcast', (req, res) => {
  try {
    const { channel, event, data } = req.body;
    
    if (!channel || !event) {
      return res.status(400).json({ error: 'Channel and event are required' });
    }
    
    console.log(`📢 Broadcasting: ${event} on channel ${channel}`);
    
    // Broadcast to all subscribers of the channel
    broadcastToChannel(channel, {
      type: 'update',
      event: event,
      data: data,
      timestamp: new Date().toISOString()
    });
    
    res.json({ success: true, message: 'Broadcast sent' });
  } catch (error) {
    console.error('Error broadcasting:', error);
    res.status(500).json({ error: error.message });
  }
});

// REST endpoint untuk broadcast ke semua client
app.post('/broadcast-all', (req, res) => {
  try {
    const { event, data } = req.body;
    
    if (!event) {
      return res.status(400).json({ error: 'Event is required' });
    }
    
    console.log(`📢 Broadcasting to all: ${event}`);
    
    broadcastToAll({
      type: 'update',
      event: event,
      data: data,
      timestamp: new Date().toISOString()
    });
    
    res.json({ success: true, message: 'Broadcast sent to all clients' });
  } catch (error) {
    console.error('Error broadcasting:', error);
    res.status(500).json({ error: error.message });
  }
});

wss.on('connection', (ws, req) => {
  console.log('Client connected');
  
  // Initialize client info
  clientInfo.set(ws, {
    userId: null,
    userName: null,
    subscribedChannels: new Set()
  });

  ws.on('message', (message) => {
    try {
      const data = JSON.parse(message.toString());
      console.log('Received:', data);

      const info = clientInfo.get(ws);
      
      // Update client info if provided
      if (data.user_id) {
        info.userId = data.user_id;
      }
      if (data.user_name) {
        info.userName = data.user_name;
      }

      // Handle subscribe action
      if (data.action === 'subscribe') {
        const channels = Array.isArray(data.channels) ? data.channels : [data.channel].filter(Boolean);
        
        channels.forEach(channel => {
          if (channel) {
            info.subscribedChannels.add(channel);
            console.log(`Client ${info.userName || info.userId} subscribed to channel: ${channel}`);
          }
        });
        
        sendToClient(ws, {
          status: 'subscribed',
          channels: Array.from(info.subscribedChannels),
          message: 'Successfully subscribed to channels'
        });
      }
      
      // Handle unsubscribe action
      else if (data.action === 'unsubscribe') {
        const channels = Array.isArray(data.channels) ? data.channels : [data.channel].filter(Boolean);
        
        channels.forEach(channel => {
          info.subscribedChannels.delete(channel);
          console.log(`Client ${info.userName || info.userId} unsubscribed from channel: ${channel}`);
        });
        
        sendToClient(ws, {
          status: 'unsubscribed',
          channels: Array.from(info.subscribedChannels),
          message: 'Successfully unsubscribed from channels'
        });
      }
      
      // Handle ping (keep-alive)
      else if (data.action === 'ping') {
        sendToClient(ws, {
          status: 'pong',
          timestamp: new Date().toISOString()
        });
      }
      
    } catch (error) {
      console.error('Error processing message:', error);
      sendToClient(ws, {
        status: 'error',
        message: 'Error processing request: ' + error.message
      });
    }
  });

  ws.on('close', () => {
    console.log('Client disconnected');
    cleanupClient(ws);
  });

  ws.on('error', (error) => {
    console.error('WebSocket error:', error);
    cleanupClient(ws);
  });

  // Send welcome message
  sendToClient(ws, {
    status: 'connected',
    message: 'Terhubung ke WebSocket server',
    timestamp: new Date().toISOString()
  });
});

// Keep-alive ping interval
setInterval(() => {
  wss.clients.forEach((client) => {
    if (client.readyState === 1) {
      sendToClient(client, {
        type: 'ping',
        timestamp: new Date().toISOString()
      });
    }
  });
}, 30000); // Every 30 seconds

console.log('WebSocket real-time update system initialized');
console.log('REST endpoints:');
console.log('  POST /broadcast - Broadcast to specific channel');
console.log('  POST /broadcast-all - Broadcast to all clients');
