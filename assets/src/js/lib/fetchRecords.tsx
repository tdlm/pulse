import { addQueryArgs } from "@wordpress/url";
import apiFetch from "@wordpress/api-fetch";
import { Records } from "../admin-dashboard/types";

export default async function fetchRecords(params: {
    action?: string;
    context?: string; 
    search?: string;
    ip?: string;
    limit?: number;
    offset?: number;
    pulse?: string;
    user_id?: number;
    signal?: AbortSignal;
}): Promise<{
    items: Records;
    objects: number;
    pages: number;
}> {
    const { action, context, search, ip, pulse, user_id, limit, offset, signal } = params;
    const response = await apiFetch({
      path: addQueryArgs("/wp-pulse/v1/records", {
        action,
        context,
        ip,
        limit,
        offset,
        pulse,
        search,
        user_id,
      }),
      method: "GET",
      parse: false,
      signal,
    });

    const data = await response.json();

    return {
      items: data['items'] as Records,
      objects: Number(response.headers.get('X-WP-Total')),
      pages: Number(response.headers.get('X-WP-TotalPages')),
    };
  }