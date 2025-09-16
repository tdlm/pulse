import { Records, Record } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: Records & {
      settings: {
        admin_url: string;
        dashboard_base_url: string;  
        live_updates_enabled: boolean;
      };
    };
    PulseAdminPulseDetail: {
      meta: Array<{
        key: string;
        value: string;
      }>;
      record: Record;
      settings: {
        admin_url: string;
      };
    };
  }
}

// This export statement is crucial for module augmentation
export {};
