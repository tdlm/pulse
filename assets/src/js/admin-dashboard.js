import {
  QueryClient,
  QueryClientProvider,
  useQuery,
} from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { useDebounce } from "@uidotdev/usehooks";
import apiFetch from "@wordpress/api-fetch";
import { createRoot } from "@wordpress/element";
import { addQueryArgs } from '@wordpress/url';
import { useQueryState } from "nuqs";
import { NuqsAdapter } from 'nuqs/adapters/react';
import TimeAgo from "react-timeago";
import { makeIntlFormatter } from "react-timeago/defaultFormatter";

import "../scss/admin-dashboard.scss";

const app = document.getElementById("pulse-dashboard-container");

const intlFormatter = makeIntlFormatter({
  locale: undefined, // string
  localeMatcher: "best fit", // 'lookup' | 'best fit',
  numberingSystem: "latn", // Intl$NumberingSystem such as 'arab', 'deva', 'hebr' etc.
  style: "long", // 'long' | 'short' | 'narrow',
  numeric: "auto", //  'always' | 'auto', Using 'auto` will convert "1 day ago" to "yesterday" etc.
});

const queryClient = new QueryClient();

function upperFirst(sentence) {
  return sentence.replace(/\b\w/g, (char) => char.toUpperCase());
}

async function fetchRecords(params) {
  const { action, context, search, ip, pulse, user_id, signal } = params;
  const response = await apiFetch({
    path: addQueryArgs("/wp-pulse/v1/records", { action, context, search, ip, pulse, user_id }),
    method: "GET",
    signal,
  });

  return response;
};

const AdminDashboardApp = () => {
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

  const { data, isLoading, isError } = useQuery({
    initialData: window.PulseAdminDashboard.records,
    keepPreviousData: true,
    refetchInterval: 10 * 1000, // 10 seconds.
    queryKey: ["records", debouncedSearch],
    queryFn: ({ signal }) => fetchRecords({
      action,
      context,
      search: debouncedSearch,
      ip,
      pulse,
      user_id,
      signal,
    }),
  });

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
      <table className="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th
              scope="col"
              id="date"
              className="manage-column column-date sorted asc"
              aria-sort="ascending"
            >
              <a href="http://localhost:8888/wp-admin/admin.php?page=wp-pulse&orderby=date&order=desc">
                <span>Date</span>
                <span className="sorting-indicators">
                  <span
                    className="sorting-indicator asc"
                    aria-hidden="true"
                  ></span>
                  <span
                    className="sorting-indicator desc"
                    aria-hidden="true"
                  ></span>
                </span>
              </a>
            </th>
            <th
              scope="col"
              id="summary"
              className="manage-column column-summary"
            >
              Summary
            </th>
            <th
              scope="col"
              id="user_id"
              className="manage-column column-user_id"
            >
              User
            </th>
            <th
              scope="col"
              id="context"
              className="manage-column column-context"
            >
              Context
            </th>
            <th scope="col" id="action" className="manage-column column-action">
              Action
            </th>
            <th scope="col" id="ip" className="manage-column column-ip">
              IP Address
            </th>
          </tr>
        </thead>
        <tbody className="the-list">
          {data.map((record) => (
            <tr key={record.id}>
              <td data-colname="Date">
                <strong>
                  <TimeAgo date={record.created_at} formatter={intlFormatter} />
                </strong>
                <br />
                <a
                  title=""
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&date=${record.created_at}`}
                >
                  <time dateTime={record.created_at}>{record.created_at}</time>
                </a>
              </td>
              <td data-colname="Description">{record.description}</td>
              <td data-colname="User">
                <a
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&user_id=${record.user_id}`}
                >
                  <img
                    src={record.gravatar_url}
                    srcSet={`${record.gravatar_url_2x} 2x`}
                    alt=""
                    width="80"
                    height="80"
                  />
                </a>
                <div>
                  <a
                    href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&user_id=${record.user_id}`}
                  >
                    {record.display_name}
                  </a>

                  <br />
                  <small>{record.user_roles.join(", ")}</small>
                </div>
              </td>
              <td data-colname="Context">
                <a
                  title=""
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&pulse=${record.pulse}`}
                >
                  {upperFirst(record.pulse)}
                </a>
                <br />
                ↳&nbsp;
                <a
                  title=""
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&pulse=${record.pulse}&context=${record.context}`}
                >
                  {upperFirst(record.context)}
                </a>
              </td>
              <td data-colname="Action">
                <a
                  title=""
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&action=${record.action}`}
                >
                  {upperFirst(record.action)}
                </a>
              </td>
              <td data-colname="IP Address">
                <a
                  title=""
                  href={`http://localhost:8888/wp-admin/admin.php?page=wp-pulse&ip=${record.ip}`}
                >
                  {record.ip}
                </a>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </form>
  );
};

if (app) {
  const root = createRoot(app);
  root.render(
    <QueryClientProvider client={queryClient}>
      <NuqsAdapter>
        <AdminDashboardApp />
        <ReactQueryDevtools initialIsOpen={false} />
      </NuqsAdapter>
    </QueryClientProvider>
  );
}
