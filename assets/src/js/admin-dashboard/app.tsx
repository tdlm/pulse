import { useQuery } from "@tanstack/react-query";
import { useDebounce } from "@uidotdev/usehooks";
import { useQueryState } from "nuqs";
import React from "react";
import fetchRecords from "../lib/fetchRecords";
import DataRow from "./components/data-row";
import HeaderRow from "./components/header-row";
import { Records } from "./types";

declare global {
  interface Window {
    PulseAdminDashboard: {
      records: any[];
    };
  }
}

/**
 * Admin dashboard app.
 * 
 * @returns The admin dashboard app.
 */
export default function AdminDashboardApp() {
  const [search, setSearch] = useQueryState("search", {
    defaultValue: "",
  });
  const [action, setAction] = useQueryState("action", {
    defaultValue: "",
  });
  const [context, setContext] = useQueryState("context", {
    defaultValue: "",
  });
  const [ip, setIp] = useQueryState("ip", {
    defaultValue: "",
  });
  const [pulse, setPulse] = useQueryState("pulse", {
    defaultValue: "",
  });
  const [user_id, setUserId] = useQueryState("user_id", {
    defaultValue: "",
  });
  const debouncedSearch = useDebounce(search, 350);

  const useFetchRecords = (debouncedSearch: string) => {
    return useQuery<Records>({
      initialData: window.PulseAdminDashboard.records,
      refetchInterval: 10 * 1000, // 10 seconds.
      queryKey: ["records", debouncedSearch],
      queryFn: ({ signal }) =>
        fetchRecords({
          action,
          context,
          search: debouncedSearch,
          ip,
          pulse,
          user_id,
          signal,
        }),
    });
  };

  const { data, isLoading, isError } = useFetchRecords(debouncedSearch);

  return (
    <form method="get" action="http://localhost:8888/wp-admin/admin.php">
      <input type="hidden" name="page" value="wp-pulse" />
      <p className="search-box">
        <label className="screen-reader-text" htmlFor="record-search-input">
          Search Records:
        </label>
        <input
          type="search"
          id="record-search-input"
          name="search"
          defaultValue={search}
          onChange={(e) => setSearch(e.target.value)}
        />
        <input
          type="submit"
          name=""
          id="search-submit"
          className="button"
          value="Search Records"
        />
      </p>
      <table className="pulse-table wp-list-table widefat fixed striped">
        <HeaderRow />
        <tbody className="the-list">
          {data.map((record) => (
            <DataRow key={record.id} record={record} />
          ))}
        </tbody>
      </table>
    </form>
  );
}
