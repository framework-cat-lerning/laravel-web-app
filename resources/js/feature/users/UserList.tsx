import { Link } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import { useAuth } from '@/contexts/AuthContext';
import { newMethod as userCreate } from '@/routes/admin/users';
import type { User } from '@/types/resource';
import { update as userEdit } from '@/routes/admin/users';

interface UserListProps {
  users: User[];
}

export default function UserList({ users }: UserListProps) {
  const { auth: { user } } = useAuth();

  return (
    <>
      {user.role ===1 && (
        <Box sx={{ mb: 1, display: 'flex', justifyContent: 'flex-end', marginBottom: 2 }}>
          <Link href={userCreate.url()}>
            <Button variant="contained" color="primary">
              ユーザー追加
            </Button>
          </Link>
        </Box>
      )}
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>ユーザ名</TableCell>
              <TableCell>メールアドレス</TableCell>
              <TableCell>権限</TableCell>
              <TableCell>作成日</TableCell>
              <TableCell>更新日</TableCell>
              <TableCell>操作</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {users.length === 0 && (
              <tr>
                <td colSpan={6} className="text-center py-4">
                  データがありません
                </td>
              </tr>
            )}
            {users.map((user) => (
              <TableRow key={user.id}>
                <TableCell>{user.id}</TableCell>
                <TableCell>{user.name}</TableCell>
                <TableCell>{user.email}</TableCell>
                <TableCell>{user.role.label}</TableCell>
                <TableCell>{user.created_at}</TableCell>
                <TableCell>{user.updated_at}</TableCell>
                <TableCell>
                  <Button
                    variant="contained" color="warning" sx={{ marginRight: 1 }}
                    href={userEdit.url({ user: user.id })}
                  >
                    編集
                  </Button>
                  <Button variant="contained" color="error" sx={{ marginRight: 1 }}>
                    削除
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </>
  );
}