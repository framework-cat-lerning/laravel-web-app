export type AuthUser = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    role: number;
    [key: string]: unknown; // This allows for additional properties...
};

export type Auth = {
    user: AuthUser;
};
