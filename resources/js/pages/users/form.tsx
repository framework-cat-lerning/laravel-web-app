import { Head } from "@inertiajs/react";
import Box from "@mui/material/Box";
import Header from "@/components/ui/Header";
import UserFormComponent from "@/feature/users/UserForm";
import type { UserFormInput } from "@/schemes/user";
import type { UserRole } from "@/types/cases";

interface UserFormProps {
    form_type: 'new' | 'edit';
    user: UserFormInput;
    options: {
        roles: UserRole[];
    };
}

export default function UserForm({ form_type, user, options }: UserFormProps) {
    return (
        <>
            <Head title={form_type === 'new' ? 'ユーザ追加' : 'ユーザ編集'} />
            <Header title={form_type === 'new' ? 'ユーザ追加' : 'ユーザ編集'} />
            <Box
                sx={{
                py: 2,
                }}
            >
                <UserFormComponent form_type={form_type} user={user} options={options} />
            </Box>
        </>
    );
}