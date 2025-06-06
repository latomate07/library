import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/Types';

export const usePermissions = () => {
    const page = usePage<PageProps>();

    const hasPermission = (permission: string): boolean => {
        const user = page.props.auth.user;
        return user?.permissions?.includes(permission) || 
               user?.roles?.includes('admin') || 
               false;
    };

    const hasRole = (role: string): boolean => {
        const user = page.props.auth.user;
        return user?.roles?.includes(role) || false;
    };

    const canView = (resource: string): boolean => {
        return hasPermission(`view ${resource}`) || hasRole('admin');
    };

    const canCreate = (resource: string): boolean => {
        return hasPermission(`create ${resource}`) || hasRole('admin');
    };

    const canUpdate = (resource: string): boolean => {
        return hasPermission(`update ${resource}`) || hasRole('admin');
    };

    const canDelete = (resource: string): boolean => {
        return hasPermission(`delete ${resource}`) || hasRole('admin');
    };

    return {
        hasPermission,
        hasRole,
        canView,
        canCreate,
        canUpdate,
        canDelete
    };
};