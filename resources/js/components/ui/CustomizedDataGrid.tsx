import { DataGrid, type GridValidRowModel } from '@mui/x-data-grid';
import type { LogTableColumn } from '@/types/resource';
import Typography from '@mui/material/Typography';

interface CustomizedDataGridProps<R extends GridValidRowModel> {
  title: string;
  logs: {
    columns: LogTableColumn[];
    rows: R[];
  };
}

export default function CustomizedDataGrid<R extends GridValidRowModel>({
  title,
  logs,
}: CustomizedDataGridProps<R>) {
  return (
    <>
      <Typography component="h2" variant="h6" sx={{ mb: 2 }}>
        {title}
      </Typography>
      <DataGrid
        checkboxSelection
        rows={logs.rows}
        columns={logs.columns}
        getRowClassName={(params) =>
          params.indexRelativeToCurrentPage % 2 === 0 ? 'even' : 'odd'
        }
        initialState={{
          pagination: { paginationModel: { pageSize: 20 } },
        }}
        pageSizeOptions={[10, 20, 50]}
        disableColumnResize
        density="compact"
        slotProps={{
          filterPanel: {
            filterFormProps: {
              logicOperatorInputProps: {
                variant: 'outlined',
                size: 'small',
              },
              columnInputProps: {
                variant: 'outlined',
                size: 'small',
                sx: { mt: 'auto' },
              },
              operatorInputProps: {
                variant: 'outlined',
                size: 'small',
                sx: { mt: 'auto' },
              },
              valueInputProps: {
                InputComponentProps: {
                  variant: 'outlined',
                  size: 'small',
                },
              },
            },
          },
        }}
      />
    </>
  );
}
