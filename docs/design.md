# Pulse Design Document

## Overview

Pulse is a WordPress plugin for tracking the activity of logged in users. Once installed, it will create its own tables in which to store records for actions performed on a site.

## User Experience (UX) Design

### Navigation & Layout

* **Menu**: WordPress menu titled "Pulse" with two sub menus: "Pulse" and "Settings."
* **Pulse Page**: WordPress listing table with pagination and search, showing "Pulse" activity records.
* **Settings Page**: WordPress settings page rendered using React. It features tabs across the top. The first and only tab for now is the 'General' tab, under which there's a setting indicating how long to keep records for.