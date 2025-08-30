type Record = {
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
};

type Records = {
  items: Record[];
  count: number;
  limit: number;
};

export type { Record, Records };
