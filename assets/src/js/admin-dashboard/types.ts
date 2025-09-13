export type PulseLink = {
	label: string;
	link: string;
};

export type Filters = {
	action?: string;
	context?: string;
	created_at?: string;
	date_range?: string;
	ip?: string;
	offset?: number;
	order_by?: string;
	order?: string;
	pulse?: string;
	user_id?: number;
};

export type Record = {
	id: number;
	created_at: string;
	description: string;
	user_id: number;
	ip: string;
	gravatar_url: string;
	gravatar_url_2x: string;
	display_name: string;
	user_roles: string[];
	pulse_label: string;
	context_label: string;
	action_label: string;
	pulse: string;
	context: string;
	action: string;
	pulse_links: PulseLink[];
	meta?: Array< {
		key: string;
		value: string;
	} >;
};

export type Records = {
	count: number;
	items: Record[];
	limit: number;
	offset: number;
	pages: number;
	users: UserOption[];
};

export type Option = {
	value: string;
	label: string;
};

export type UserOption = Option & {
	id: number;
	email: string;
	avatar_urls: string[];
	name: string;
};

export {};
