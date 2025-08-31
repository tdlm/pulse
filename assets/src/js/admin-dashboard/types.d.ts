import { Records } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: Records;
  }
}

export {};