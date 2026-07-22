import Box from '@mui/material/Box';
import Text from '@mui/material/Typography';

interface InputRowProps {
  label: string;
  children: React.ReactNode;
}

export default function InputRow({
  label,
  children
}: InputRowProps) {
  return (
    <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 2 }}>
      <Text sx={{ fontSize: 16, fontWeight: 'bold', flex: 2 }}>{label}</Text>
      <Box sx={{ flex: 10 }}>{children}</Box>
    </Box>
  );
}
