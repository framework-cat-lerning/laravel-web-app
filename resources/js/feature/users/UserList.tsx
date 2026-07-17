import { Link, router } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Paper from '@mui/material/Paper';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableContainer from '@mui/material/TableContainer';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import { useState } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { newMethod as userCreate, deleteMethod as userDelete } from '@/routes/admin/users';
import { update as userEdit } from '@/routes/admin/users';
import { show } from '@/routes/admin/users';
import type { User } from '@/types/resource';

interface UserListProps {
  users: User[];
}

export default function UserList({ users }: UserListProps) {
  const { auth: { user } } = useAuth();

  const [deleteTarget, setDeleteTarget] = useState<User | null>(null);

  const handleDeleteClick = (target: User) => {
    setDeleteTarget(target);
  };

  const handleCloseModal = () => {
    setDeleteTarget(null);
  };

  const handleConfirmDelete = () => {
    if (!deleteTarget) {
      return;
    }

    router.delete(userDelete.url({ user: deleteTarget.id }), {
      onSuccess: () => {
        setDeleteTarget(null);
      },
    });
  };

  return (
    <>
      {user.role === 1 && (
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
            {users.map((u) => (
              <TableRow key={u.id} onClick={() => router.visit(show.url({ user: u.id }))}>
                <TableCell>{u.id}</TableCell>
                <TableCell>{u.name}</TableCell>
                <TableCell>{u.email}</TableCell>
                <TableCell>{u.role.label}</TableCell>
                <TableCell>{u.created_at}</TableCell>
                <TableCell>{u.updated_at}</TableCell>
                <TableCell>
                  <Button
                    variant="contained" color="warning" sx={{ marginRight: 1 }}
                    href={userEdit.url({ user: u.id })}
                  >
                    編集
                  </Button>
                  <Button
                    variant="contained" color="error" sx={{ marginRight: 1 }}
                    onClick={() => handleDeleteClick(u)}
                  >
                    削除
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      <Dialog open={!!deleteTarget} onClose={handleCloseModal}>
        <DialogTitle>ユーザーの削除</DialogTitle>
        <DialogContent>
          <DialogContentText>
            「{deleteTarget?.name}」を削除します。この操作は取り消せません。よろしいですか？
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleCloseModal}>キャンセル</Button>
          <Button onClick={handleConfirmDelete} color="error" variant="contained">
            削除する
          </Button>
        </DialogActions>
      </Dialog>
    </>
  );
}