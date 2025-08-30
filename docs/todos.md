# TODOS

## Core Features

- [x] Activity Logging: Core logging system (`Log::log()`)
- [x] Activity Logging: Database storage with metadata
- [ ] Activity Logging: WordPress action coverage
    - [ ] ACF
    - [ ] BBPress
    - [ ] Blogs
    - [ ] BuddyPress
    - [ ] Comments
    - [ ] Easy Digital Downloads
    - [ ] Editor
    - [ ] Gravity Forms
    - [x] Installer
    - [ ] Jetpack
    - [ ] Media
    - [ ] Menus
    - [ ] Mercator
    - [ ] Posts
    - [ ] Settings
    - [ ] Taxonomies
    - [ ] Two Factor
    - [x] User Switching
    - [ ] Users
    - [ ] Widgets
    - [ ] WooCommerce
    - [ ] WordPress SEO
- [_] Dashboard Detail: Expandable row details
- [x] Dashboard Detail: Metadata display (JSON format)
- [x] Dashboard Detail: User information (avatar, roles, IP)
- [x] Dashboard Detail: Timestamp with relative time
- [x] CLI: Data export (JSON/CSV via `wp pulse query`)
- [x] CLI: Database reset
- [x] CLI: Database migration
- [ ] Dashboard: Web interface export buttons
- [x] Dashboard: Search functionality with debouncing
- [x] Dashboard: Backend filtering (action, context, ip, pulse, user_id)
- [x] Dashboard: Table view of activity records
- [x] Dashboard: Real-time updates (10s interval)
- [x] Dashboard: Main dashboard page
- [x] Dashboard: React-based interface
- [x] Dashboard: Search input
- [x] Dashboard: Data table with sorting
- [ ] Dashboard: UI filter controls/dropdowns using React Query
- [ ] Dashboard: Pagination using React Query
- [ ] Dashboard: Filter UI components
- [ ] Settings: Settings page
- [ ] Settings: Data retention settings
- [ ] Settings: General configuration options
- [x] Developer Integration: Base Pulse class for extension
- [x] Developer Integration: Filter hook (`wp_pulse_pulses`) 
- [x] Developer Integration: Public Log API
- [ ] Developer Integration: Documentation/examples

## Technical Debt
- [ ] Add js lint / fixing via wp-scripts
- [ ] Fix TypeScript type error in fetchRecords
- [ ] Add proper error handling
- [ ] Improve loading states
- [ ] Add GitHub action to deploy to WordPress SVN

## Future State
- [ ] Pro Version
    - [ ] Dataviz Dashboard (activity by user, articles published)
    - [ ] User Profile Dataviz
    - [ ] Editor heatmap
    - [ ] Session tracking

