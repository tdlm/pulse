import { addQueryArgs } from "@wordpress/url";
import apiFetch from "@wordpress/api-fetch";
import { Records } from "../admin-dashboard/types";

export default async function fetchRecords(params: {
    action?: string;
    context?: string; 
    search?: string;
    ip?: string;
    pulse?: string;
    user_id?: string;
    signal?: AbortSignal;
}): Promise<Records> {
    const { action, context, search, ip, pulse, user_id, signal } = params;
    const response = await apiFetch({
      path: addQueryArgs("/wp-pulse/v1/records", {
        action,
        context,
        search,
        ip,
        pulse,
        user_id,
      }),
      method: "GET",
      signal,
    });
  
    return response as Records;
  }