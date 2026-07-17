import { Head } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Header from '@/components/ui/Header';
import UserList from '@/feature/users/UserList';
import type { UserResource } from '@/types';

interface UsersIndexProps {
  users: UserResource;
  request: any;
}

export default function UsersIndex({ users }: UsersIndexProps) {
  return (
    <>
      <Head title="ユーザー管理" />
      <Header title="ユーザー管理" />
      <Box
        sx={{
          py: 2,
        }}
      >
        <UserList users={users.data} />
      </Box>
    </>
  );
}
