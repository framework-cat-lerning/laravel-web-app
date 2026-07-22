export type UserRole = {
  id: number;
  label: string;
};

export type ProductStatus = {
  id: number;
  label: string;
};

export type Product = {
  id: number;
  name: string;
  description: string|null;
  price: number;
  status: ProductStatus;
  created_at: string|null;
  updated_at: string|null;
}

export type ProductResource = {
  data: Product[];
};

export type User = {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  created_at: string|null;
  updated_at: string|null;
}

export type UserResource = {
  data: User[];
};

export type ProductInventory = {
  id: number;
  product_id: number;
  name: string;
  description: string|null;
  price: number;
  status: ProductStatus;
  quantity: number;
  created_at: string|null;
  updated_at: string|null;
}

export type ProductInventoryResource = {
  data: ProductInventory[];
};

export type ChartData = {
  title: string;
  value: string;
  interval: string;
  trend: 'up' | 'down' | 'neutral';
  data: number[];
}

export type LogTableColumn = {
  field: string;
  headerName: string;
  headerAlign: 'left' | 'center' | 'right';
  align: 'left' | 'center' | 'right';
  flex: number;
  minWidth: number;
}
export type ConsumptionLogData = {
  id: number;
  product_name: string;
  consumption_at: string;
  quantity: number;
  total_amount: number;
}
export type ConsumptionLogDataResource = {
  columns: LogTableColumn[],
  rows: ConsumptionLogData[];
}
