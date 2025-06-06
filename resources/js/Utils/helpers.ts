export const formatDate = (date: string | Date): string => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

export const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

export const formatDateForInput = (date: string | Date): string => {
    const d = new Date(date);
    return d.toISOString().split('T')[0];
};

export const truncateText = (text: string, length: number = 100): string => {
    return text.length > length ? text.substring(0, length) + '...' : text;
};