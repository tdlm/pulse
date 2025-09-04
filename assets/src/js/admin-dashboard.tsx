import React from 'react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { createRoot } from 'react-dom/client';
import { NuqsAdapter } from 'nuqs/adapters/react'; // eslint-disable-line import/no-unresolved
import AdminDashboardApp from './admin-dashboard/app';

import '../scss/admin-dashboard.scss';

const app = document.getElementById( 'pulse-dashboard-container' );

const queryClient = new QueryClient();

if ( app ) {
	const root = createRoot( app );
	root.render(
		<QueryClientProvider client={ queryClient }>
			<NuqsAdapter>
				<AdminDashboardApp />
				<ReactQueryDevtools initialIsOpen={ false } />
			</NuqsAdapter>
		</QueryClientProvider>
	);
}
