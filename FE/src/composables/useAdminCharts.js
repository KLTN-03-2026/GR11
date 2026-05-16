let chartsLoaded = false;
let loadPromise = null;

/**
 * Dynamically load ApexCharts, Morris, Chartist and chart-custom only when needed.
 */
export function useAdminCharts() {
  async function loadCharts() {
    if (chartsLoaded) return;
    if (loadPromise) return loadPromise;

    loadPromise = (async () => {
      await import('../../public/Admin/js/apexcharts.js');
      await import('../../public/Admin/js/morris.js');
      await import('../../public/Admin/js/chartist/chartist.min.js');
      await import('../../public/Admin/js/chart-custom.js');
      chartsLoaded = true;
    })();

    return loadPromise;
  }

  return { loadCharts };
}
