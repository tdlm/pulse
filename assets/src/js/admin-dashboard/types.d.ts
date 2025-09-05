import { Records } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: Records & {
      settings: {
        admin_url: string,
        dashboard_base_url: string,  
        live_updates_enabled: boolean
      }
    };
  }
}

export {};