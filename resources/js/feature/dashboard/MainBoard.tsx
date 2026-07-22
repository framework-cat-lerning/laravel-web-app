import Box from '@mui/material/Box';
import Grid from '@mui/material/Grid';
import Typography from '@mui/material/Typography';
import Copyright from '@/components/parts/Copyright';
import CustomizedDataGrid from '@/components/ui/CustomizedDataGrid';
import StatCard from '@/components/ui/StatCard';
import { useAuth } from '@/contexts/AuthContext';
import type {
  ChartData,
  ConsumptionLogDataResource,
  InventoryLogDataResource
} from '@/types/resource';

interface MainGridProps {
  charts: {
    products?: ChartData[];
  }
  logs: {
    inventories?: InventoryLogDataResource;
    consumptions?: ConsumptionLogDataResource;
  }
}

export default function MainGrid({ charts, logs }: MainGridProps) {
  const {auth} = useAuth();

  return (
    <Box sx={{ width: '100%', maxWidth: { sm: '100%', md: '1700px' } }}>
      {/* cards */}
      <Typography component="h2" variant="h6" sx={{ mb: 2 }}>
        Overview
      </Typography>
      <Grid
        container
        spacing={2}
        columns={12}
        sx={{ mb: (theme) => theme.spacing(2) }}
      >
      {auth.user?.role === 2 && (
        <>
          {charts.products?.map((card, index) => (
            <Grid key={index} size={{ xs: 12, sm: 6, lg: 3 }}>
              <StatCard {...card} />
            </Grid>
          ))}
        </>
      )}
      </Grid>

      {auth.user?.role === 1 && logs.inventories ? (
        <CustomizedDataGrid title="在庫ログ" logs={logs.inventories} />
      ) : null}

      {auth.user?.role === 2 && logs.consumptions ? (
        <CustomizedDataGrid title="販売ログ" logs={logs.consumptions} />
      ) : null}

      <Copyright sx={{ my: 4 }} />
    </Box>
  );
}
