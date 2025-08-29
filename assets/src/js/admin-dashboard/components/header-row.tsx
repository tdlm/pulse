import React from "react";

/**
 * Header row for the admin dashboard.
 * 
 * @returns The header row.
 */
export default function HeaderRow() {
  return (
    <thead>
      <tr>
        <th
          scope="col"
          id="expand-toggle"
          className="manage-column column-expand-toggle"
        ></th>
        <th
          scope="col"
          id="date"
          className="manage-column column-date sorted asc"
          aria-sort="ascending"
        >
          <a href="http://localhost:8888/wp-admin/admin.php?page=wp-pulse&orderby=date&order=desc">
            <span>Date</span>
            <span className="sorting-indicators">
              <span className="sorting-indicator asc" aria-hidden="true"></span>
              <span
                className="sorting-indicator desc"
                aria-hidden="true"
              ></span>
            </span>
          </a>
        </th>
        <th scope="col" id="summary" className="manage-column column-summary">
          Summary
        </th>
        <th scope="col" id="user_id" className="manage-column column-user_id">
          User
        </th>
        <th scope="col" id="context" className="manage-column column-context">
          Context
        </th>
        <th scope="col" id="action" className="manage-column column-action">
          Action
        </th>
      </tr>
    </thead>
  );
}
