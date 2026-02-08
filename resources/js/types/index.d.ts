// Core types for VoltPanel
export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string | null;
  created_at: string;
  updated_at: string;
}

export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
  duration?: number;
}

export interface Theme {
  primary: string;
  primaryForeground: string;
  secondary?: string;
  secondaryForeground?: string;
  success?: string;
  successForeground?: string;
  danger?: string;
  dangerForeground?: string;
  warning?: string;
  warningForeground?: string;
  info?: string;
  infoForeground?: string;
}

export interface NavigationItem {
  label: string;
  url: string;
  icon?: string;
  active?: boolean;
  badge?: string | number;
  badgeColor?: string;
  children?: NavigationItem[];
}

export interface Tenant {
  id: number | string;
  name: string;
  slug?: string;
  logo?: string | null;
}

// Widget types
export interface WidgetData {
  [key: string]: any;
}

export interface Widget {
  type: string;
  data: WidgetData;
  columnSpan: number;
}

export interface ChartDataset {
  label: string;
  data: number[];
  backgroundColor?: string | string[];
  borderColor?: string | string[];
  borderWidth?: number;
  fill?: boolean;
  tension?: number;
}

export interface ChartData {
  labels: string[];
  datasets: ChartDataset[];
}

export interface ChartOptions {
  responsive?: boolean;
  maintainAspectRatio?: boolean;
  scales?: any;
  plugins?: any;
  elements?: any;
}

export interface AdvancedChartWidgetData extends WidgetData {
  heading?: string;
  description?: string | null;
  type: 'line' | 'bar' | 'pie' | 'doughnut' | 'radar' | 'polarArea' | 'scatter' | 'bubble' | 'mixed';
  chartData: ChartData;
  options: ChartOptions;
  showExport: boolean;
  exportFilename: string;
  realtime: boolean;
  realtimeInterval: number;
  realtimeCallback: (() => Promise<ChartData>) | null;
  colorScheme: 'default' | 'pastel' | 'vibrant' | 'earth' | 'ocean' | 'sunset';
}

// Resource types
export interface Field {
  name: string;
  label: string;
  type: string;
  required?: boolean;
  placeholder?: string;
  helpText?: string;
  default?: any;
  options?: any;
  rules?: string[];
  dependsOn?: string;
  visible?: boolean;
  readonly?: boolean;
}

export interface Resource {
  id: number | string;
  [key: string]: any;
}

export interface ResourceCollection {
  data: Resource[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
}
