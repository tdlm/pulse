# Contributing to Pulse

Thank you for your interest in contributing to Pulse! This guide will help you get started with the development setup and understand the available commands.

## Development Setup

### Requirements

- **Node.js**: >= 22.0.0
- **npm**: >= 10.0.0
- **PHP**: >= 7.4
- **Composer**: Latest version
- **WordPress**: Compatible with WordPress 6.4+

### Dependencies

The project uses several key dependencies:

**Frontend/JavaScript:**
- **@wordpress/scripts**: WordPress build toolchain for assets
- **@wordpress/env**: Local WordPress development environment
- **React**: UI components (via WordPress React)
- **TypeScript**: Type safety for JavaScript code
- **@tanstack/react-query**: Data fetching and state management
- **React Select**: Enhanced select components

**Backend/PHP:**
- **WordPress Coding Standards (WPCS)**: PHP code formatting and linting
- **WordPress Stubs**: Type definitions for WordPress functions
- **WP-CLI Stubs**: Type definitions for WP-CLI commands

### Environment Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/tdlm/pulse.git
   cd pulse
   ```

2. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

3. **Install PHP dependencies:**
   ```bash
   composer install
   ```

4. **Start the WordPress development environment:**
   ```bash
   npm run env:start
   ```

5. **Build assets:**
   ```bash
   npm run assets:build
   ```

The development environment uses `@wordpress/env` which provides a Docker-based WordPress installation with the plugin automatically activated.

## Commands

### NPM Commands

#### Asset Management
- **`npm run assets:build`** - Build production-ready assets from source files
- **`npm run assets:start`** - Start development server with file watching
- **`npm run assets:start:hot`** - Start development server with hot module replacement

#### WordPress Environment
- **`npm run env:start`** - Start the local WordPress development environment
- **`npm run env:stop`** - Stop the local WordPress development environment
- **`npm run env:reset`** - Destroy and recreate the development environment
- **`npm run env:destroy`** - Completely remove the development environment
- **`npm run env:shell`** - Open a bash shell in the WordPress container
- **`npm run env:cli`** - Run WP-CLI commands in the WordPress environment
- **`npm run env:logs`** - View logs from the WordPress environment

#### Code Quality
- **`npm run lint-js`** - Lint JavaScript/TypeScript files
- **`npm run format-js`** - Format and fix JavaScript/TypeScript files
- **`npm run lint-php`** - Lint PHP files using WordPress Coding Standards
- **`npm run format-php`** - Format and fix PHP files using WordPress Coding Standards

#### Internationalization
- **`npm run lang:i18n`** - Generate .pot file for translations
- **`npm run lang:mo`** - Compile .po files to .mo files
- **`npm run lang`** - Run both i18n commands (generate .pot and compile .mo)

#### Build & Distribution
- **`npm run zip`** - Create a distribution zip file of the plugin

#### Composer Integration
- **`npm run composer`** - Run composer commands within the WordPress environment

### Composer Commands

- **`composer lint`** - Run PHP linting using WordPress Coding Standards
- **`composer fix`** - Automatically fix PHP code formatting issues

### Development Workflow

1. Start the development environment: `npm run env:start`
2. Start asset watching: `npm run assets:start`
3. Make your changes
4. Test your changes in the WordPress admin
5. Run linting: `npm run lint-js` and `npm run lint-php`
6. Fix any formatting issues: `npm run format-js` and `npm run format-php`
7. Build final assets: `npm run assets:build`
8. Create a pull request

### Code Standards

- **JavaScript/TypeScript**: Follow WordPress JavaScript coding standards
- **PHP**: Follow WordPress PHP coding standards (enforced by WPCS)
- **React**: Use functional components with hooks
- **CSS/SCSS**: Follow WordPress CSS coding standards

### File Structure

- `assets/src/js/` - Source JavaScript/TypeScript files
- `assets/src/scss/` - Source SCSS files
- `assets/build/` - Compiled assets (auto-generated)
- `includes/classes/` - PHP classes following PSR-4 autoloading
- `templates/` - PHP template files
- `languages/` - Translation files
