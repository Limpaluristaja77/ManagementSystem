export type UserRole = {
    id: number;
    name: string;
};

export type ManagedUser = {
    id: number;
    name: string;
    email: string;
    is_active?: boolean;
    deactivated_at?: string | null;
    roles?: UserRole[];
};
