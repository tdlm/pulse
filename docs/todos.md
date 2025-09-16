# TODOS

## Core Features

- [ ] Activity Logging:
    - [x] Core logging system (`Log::log()`)
    - [x] Database storage with metadata
    - [ ] WordPress action coverage p0
        - [x] Installer
        - [x] Media
        - [x] Posts
        - [x] User Switching
        - [x] Users
        - [ ] Blogs
        - [ ] Comments
        - [ ] Menus
        - [ ] Plugin Editor
        - [ ] Settings
        - [x] Taxonomies
        - [ ] Theme Editor
        - [ ] Widgets
    - [ ] WordPress action coverage p1
        - [ ] ACF
        - [ ] AISEO
        - [ ] BBPress
        - [ ] BuddyPress
        - [ ] Easy Digital Downloads
        - [ ] Gravity Forms
        - [ ] Jetpack
        - [ ] Mercator
        - [ ] Rank Math SEO
        - [ ] Two Factor
        - [ ] WooCommerce
        - [ ] WordPress SEO
- [x] Dashboard Detail
    - [x] Expandable row details
    - [x] Metadata display (JSON format)
    - [x] User information (avatar, roles, IP)
    - [x] Timestamp with relative time
- [x] Settings
    - [x] Settings page
    - [x] General configuration options
    - [x] Records reset w/ warning
    - [x] Data retention settings
- [x] CLI
    - [x] Data export (JSON/CSV via `wp pulse query`)
        - [x] Bring up to date with Database::get_records() params
    - [x] Database reset
    - [x] Database migration
- [x] Dashboard
    - [x] Search functionality with debouncing
    - [x] Backend filtering (action, context, ip, pulse, user_id)
    - [x] Table view of activity records
    - [x] Real-time updates (10s interval)
    - [x] Main dashboard page
    - [x] React-based interface
    - [x] Search input
    - [x] Data table with sorting
    - [x] UI filter controls/dropdowns using React Query
    - [x] Pagination using React Query
    - [x] Filter UI components
    - [x] Web interface export buttons
- [ ] Developer Integration
    - [x] Base Pulse class for extension
    - [x] Filter hook (`wp_pulse_pulses`) 
    - [x] Public Log API
    - [ ] Documentation/examples
- [ ] Object Detail View: View details and history of a particular object (user, post).
    - [x] Set up detail page
    - [x] Make detail page pretty
    - [ ] User history view
    - [ ] Post history view
- [x] Cron
    - [x] Delete record / meta older than retention date
- [ ] Multi site compatibility

## Technical Debt
- [x] Add js lint / fixing via wp-scripts
- [x] Fix TypeScript type error in fetchRecords
- [ ] Add proper error handling
- [ ] Improve loading states
- [ ] Take care of inline TODOs
- [ ] Add unit tests
- [ ] Add GitHub action to deploy to WordPress SVN
- [ ] Refine icon

## Pre v1.0.0 Release Checks
- [ ] Make sure all functions have "@since v1.0.0" tag
- [ ] Make sure all text has i18n and escaping
- [ ] Make sure all extensible items have filters

## Future State
- [ ] Pro Version
    - [ ] Dataviz Dashboard (activity by user, articles published)
    - [ ] User Profile Dataviz
    - [ ] Editor heatmap
    - [ ] Session tracking

