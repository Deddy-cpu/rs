import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    
    const user = computed(() => page.props.auth.user);
    
    const isAdmin = computed(() => {
        return user.value?.role === 'admin';
    });
    
    const isDokter = computed(() => {
        return user.value?.role === 'dokter';
    });
    
    const isKasir = computed(() => {
        return user.value?.role === 'kasir';
    });
    
    const isPendaftaran = computed(() => {
        return user.value?.role === 'pendaftaran';
    });
    
    const hasRole = (role) => {
        return user.value?.role === role;
    };
    
    const hasAnyRole = (roles) => {
        return roles.includes(user.value?.role);
    };
    
    return {
        user,
        isAdmin,
        isDokter,
        isKasir,
        isPendaftaran,
        hasRole,
        hasAnyRole
    };
}
