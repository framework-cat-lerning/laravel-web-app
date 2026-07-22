import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import Button from '@mui/material/Button';
import DialogActions from '@mui/material/DialogActions';

interface ConfirmDialogProps {
  open: boolean;
  title: string;
  message: string | React.ReactNode;
  onOK: () => void;
  onCancel: () => void;
}

export default function ConfirmDialog({
  open, title, message, onOK, onCancel
}: ConfirmDialogProps) {
  return (
    <Dialog open={open} onClose={onCancel}>
      <DialogTitle>{title}</DialogTitle>
      <DialogContent>
        {typeof message === 'string' ?
          <DialogContentText>{message}</DialogContentText> :
          message as React.ReactNode
        }
      </DialogContent>
      <DialogActions>
        <Button onClick={onCancel}>キャンセル</Button>
        <Button onClick={onOK} color="primary" variant="contained">OK</Button>
      </DialogActions>
    </Dialog>
  );
}