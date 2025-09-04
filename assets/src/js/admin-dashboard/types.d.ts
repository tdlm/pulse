import { Records } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: Records & {
      admin_url: string,
      dashboard_base_url: string
    };
  }
}

export {};