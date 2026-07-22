import { Head } from '@inertiajs/react';
import Header from '@/components/ui/Header';
import MainGrid from '@/components/ui/MainGrid';
import type { ChartData } from '@/types/resource';

interface DashboardProps {
  charts: {
    products?: ChartData[];
  }
}

export default function Dashboard({ charts }: DashboardProps) {
  return (
    <>
      <Head title="ダッシュボード" />
      <Header title="ダッシュボード" isSearch={true} />
      <MainGrid charts={charts} />
    </>
  );
}
