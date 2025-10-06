/**
 * Composable for handling poli layanan routes
 * Provides consistent route mapping for all poli service types
 */

export function usePoliRoutes() {
  // Map service types to correct route names
  const routeMap = {
    'umum': 'dokter.poli_layanan.poli_umum',
    'gigi': 'dokter.poli_layanan.poli_gigi',
    'kia': 'dokter.poli_layanan.kia',
    'laboratorium': 'dokter.poli_layanan.laboratorium',
    'apotek': 'dokter.poli_layanan.apotek'
  }

  /**
   * Get the correct route name for a service type
   * @param {string} serviceType - The service type (umum, gigi, kia, laboratorium, apotek)
   * @param {string} fallback - Fallback route name if service type not found
   * @returns {string} The route name
   */
  const getRouteName = (serviceType, fallback = 'dokter.poli_layanan.poli_umum') => {
    return routeMap[serviceType] || fallback
  }

  /**
   * Apply filters and navigate to the correct route
   * @param {Object} params - Filter parameters
   * @param {string} serviceType - Current service type
   * @param {Function} router - Inertia router instance
   * @param {Function} route - Ziggy route function
   */
  const applyFilters = (params, serviceType, router, route) => {
    const urlParams = new URLSearchParams()
    
    if (params.search) urlParams.append('search', params.search)
    if (params.kunjungan) urlParams.append('kunjungan', params.kunjungan)
    if (params.date) urlParams.append('date', params.date)
    
    const routeName = getRouteName(serviceType)
    router.get(route(routeName), Object.fromEntries(urlParams), {
      preserveState: true,
      replace: true
    })
  }

  return {
    routeMap,
    getRouteName,
    applyFilters
  }
}
