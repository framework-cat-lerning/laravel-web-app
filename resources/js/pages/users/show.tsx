import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Header from '@/components/ui/Header';
import UserDetail from '@/feature/users/UserDetail';
import { index } from '@/routes/admin/users';
import type { User } from '@/types';

interface UsersShowProps {
  user: {
    data: User;
  };
}

export default function UsersShow({ user }: UsersShowProps) {
  return (
    <>
      <Head title="ユーザー管理" />
      <Header title="ユーザー詳細" parents={[{ title: 'ユーザー管理', href: index.url() }]} />
      <Box
        sx={{
          py: 2,
        }}
      >
        <UserDetail user={user.data} />
      </Box>
    </>
  );
}
