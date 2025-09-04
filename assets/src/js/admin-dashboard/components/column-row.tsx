/* eslint-disable import/no-extraneous-dependencies */

import { __ } from "@wordpress/i18n";
import React from "react";

type ColumnRowProps = {
  order: string;
  orderBy: string;
  setOrder: (order: string) => void;
  setOrderBy: (order_by: string) => void;
};

/**
 * Header row for the admin dashboard.
 *
 * @return The header row.
 */
export default function ColumnRow({
  order,
  orderBy,
  setOrder,
  setOrderBy,
}: ColumnRowProps) {
  return (
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
        <a
          href={`/admin.php?page=wp-pulse&orderby=${orderBy}&order=${order}`}
          onClick={(e) => {
            e.preventDefault();
            setOrderBy(orderBy);
            setOrder(order === "asc" ? "desc" : "asc");
          }}
        >
          <span>{__("Date", "pulse")}</span>
          <span className="sorting-indicators">
            <span className="sorting-indicator asc" aria-hidden="true"></span>
            <span className="sorting-indicator desc" aria-hidden="true"></span>
          </span>
        </a>
      </th>
      <th scope="col" id="summary" className="manage-column column-summary">
        {__("Summary", "pulse")}
      </th>
      <th scope="col" id="user_id" className="manage-column column-user_id">
        {__("User", "pulse")}
      </th>
      <th scope="col" id="context" className="manage-column column-context">
        {__("Context", "pulse")}
      </th>
      <th scope="col" id="action" className="manage-column column-action">
        {__("Action", "pulse")}
      </th>
    </tr>
  );
}
