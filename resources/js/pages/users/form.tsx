import Header from "@/components/ui/Header";
import { Head } from "@inertiajs/react";
import Box from "@mui/material/Box";
import type { User } from "@/types/resource";
import UserFormComponent from "@/feature/users/UserForm";
import type { UserRole } from "@/types/cases";
import type { UserFormInput } from "@/schemes/user";

interface UserFormProps {
    form_type: 'new' | 'edit';
    user: UserFormInput;
    options: {
        roles: UserRole[];
    };
}

export default function UserForm({ form_type, user, options }: UserFormProps) {
    console.log(options);
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