import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Stack from '@mui/material/Stack';
import { alpha } from '@mui/material/styles';
import Header from '@/components/ui/Header';
import MainGrid from '@/components/ui/MainGrid';

export default function Dashboard() {
  return (
    <>
      <Head title="ダッシュボード" />
      <Box
        component="main"
        sx={(theme) => ({
          flexGrow: 1,
          backgroundColor: theme.vars
            ? `rgba(${theme.vars.palette.background.defaultChannel} / 1)`
            : alpha(theme.palette.background.default, 1),
          overflow: 'auto',
        })}
      >
        <Stack
          spacing={2}
          sx={{
            alignItems: 'center',
            mx: 3,
            pb: 10,
            mt: { xs: 8, md: 0 },
          }}
        >
        </Stack>
        <Header />
        <MainGrid />
      </Box>
    </>
  );
}
