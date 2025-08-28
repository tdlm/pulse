import { createRoot } from "@wordpress/element";

import "../scss/admin-dashboard.scss";

const app = document.getElementById("pulse-dashboard-container");

const AdminDashboardApp = () => (
  <form>
    <p className="search-box">
      <label className="screen-reader-text" htmlFor="record-search-input">
        Search Records:
      </label>
      <input
        type="search"
        id="record-search-input"
        name="search"
        defaultValue=""
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
            <a href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;orderby=date&amp;order=desc">
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
          <th scope="col" id="ip" className="manage-column column-ip">
            IP Address
          </th>
        </tr>
      </thead>
      <tbody className="the-list">
        <tr>
          <td data-colname="Date">
            <strong>
              <time dateTime="2025-08-27T04:27:07+0000">17 hours ago</time>
            </strong>
            <br />
            <a
              title=""
              href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;date=2025/08/27"
            >
              <time dateTime="2025-08-27T04:27:07+0000">2025/08/27</time>
            </a>
            <br />
            04:27:07 AM GMT+0000
          </td>
          <td data-colname="Summary">"Hello Dolly" plugin activated</td>
          <td data-colname="User">
            <a href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;user_id=1">
              <img
                src="https://secure.gravatar.com/avatar/be3221a6fac131657111728b4d912a877ec158b123d5db3afef3bd8a59784ece?s=80&amp;d=mm&amp;r=g"
                srcSet="https://secure.gravatar.com/avatar/be3221a6fac131657111728b4d912a877ec158b123d5db3afef3bd8a59784ece?s=160&amp;d=mm&amp;r=g 2x"
                alt=""
                width="80"
                height="80"
              />{" "}
              admin
            </a>
            <br />
            <small>Administrator</small>
          </td>
          <td data-colname="Context">
            <a
              title=""
              href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;connector=installer"
            >
              Installer
            </a>
            <br />
            ↳&nbsp;
            <a
              title=""
              href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;connector=installer&amp;context=plugins"
            >
              Plugins
            </a>
          </td>
          <td data-colname="Action">
            <a
              title=""
              href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;action=activated"
            >
              Activated
            </a>
          </td>
          <td data-colname="IP Address">
            <a
              title=""
              href="http://localhost:8888/wp-admin/admin.php?page=wp_stream&amp;ip=192.168.97.1"
            >
              192.168.97.1
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
);

if (app) {
  const root = createRoot(app);
  root.render(<AdminDashboardApp />);
}
