import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import Text from '@mui/material/Typography';
import { Controller } from 'react-hook-form';

interface InputRowProps {
  label: string;
  target_control?: any;
  input_name?: string;
  target_errors?: {
    message: string;
  };
  children?: React.ReactNode;
}

export default function InputRow({
  label,
  target_control,
  input_name,
  target_errors = {
    message: ''
  },
  children
}: InputRowProps) {
  if (!input_name) {
    return null;
  }

  return (
    <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2 }}>
      <Text sx={{ fontSize: 16, fontWeight: 'bold', flex: 2 }}>{label}</Text>
      <Box sx={{ flex: 10 }}>
        {children ? children : (
          <Controller
            control={target_control}
            name={input_name}
            render={({ field }) => (
              <TextField
                {...field}
                value={field.value ?? ''}
                fullWidth
                label={label}
                error={Boolean(target_errors)}
                helperText={target_errors?.message as string}
              />
            )}
          />)}
      </Box>
    </Box>
  );
}
