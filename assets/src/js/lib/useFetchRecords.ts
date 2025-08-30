/// <reference path="../admin-dashboard/types.d.ts" />
import { useQuery } from "@tanstack/react-query";
import fetchRecords from "./fetchRecords";
import { Records } from "../admin-dashboard/types";

export default function useFetchRecords(
  debouncedSearch: string,
  action: string,
  context: string,
  ip: string,
  offset: number,
  pulse: string,
  user_id: number
) {
  return useQuery<{
    items: Records;
    objects: number;
    pages: number;
  }>({
    initialData: {
      ...window.PulseAdminDashboard,
    },
    refetchInterval: 10 * 1000, // 10 seconds.
    queryKey: [
      "records",
      action,
      context,
      debouncedSearch,
      ip,
      window.PulseAdminDashboard.limit,
      offset,
      pulse,
      user_id,
    ],
    queryFn: ({ signal }) =>
      fetchRecords({
        action,
        context,
        search: debouncedSearch,
        ip,
        limit: window.PulseAdminDashboard.limit,
        offset,
        pulse,
        user_id,
        signal,
      }),
  });
}
