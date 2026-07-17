import { Head } from '@inertiajs/react';
import Header from '@/components/ui/Header';
import MainGrid from '@/components/ui/MainGrid';

export default function Dashboard() {
  return (
    <>
      <Head title="ダッシュボード" />
      <Header title="ダッシュボード" isSearch={true} />
      <MainGrid />
    </>
  );
}
