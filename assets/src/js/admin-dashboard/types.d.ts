import { Records } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: {
      items: Records;
      objects: number;
      limit: number;
      pages: number;
    };
  }
}

export {};