import { Head } from '@inertiajs/react';
import Header from '@/components/ui/Header';
import MainBoard from '@/feature/dashboard/MainBoard';
import type {
  ChartData,
  ConsumptionLogDataResource,
  InventoryLogDataResource
} from '@/types/resource';

interface DashboardProps {
  charts: {
    products?: ChartData[];
  }
  logs: {
    consumptions?: ConsumptionLogDataResource
    inventories?: InventoryLogDataResource
  }
}

export default function Dashboard({ charts, logs }: DashboardProps) {
  return (
    <>
      <Head title="ダッシュボード" />
      <Header title="ダッシュボード" isSearch={true} />
      <MainBoard charts={charts} logs={logs} />
    </>
  );
}
