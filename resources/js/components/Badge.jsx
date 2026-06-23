/*
 * Port of resources/views/components/badge.blade.php — a small pill badge with
 * a color key. Same color map and classes as the Blade component.
 */
const COLOR_CLASSES = {
    gray: 'bg-gray-100 text-gray-800',
    red: 'bg-red-100 text-red-800',
    orange: 'bg-orange-100 text-orange-800',
    amber: 'bg-amber-100 text-amber-800',
    yellow: 'bg-yellow-100 text-yellow-800',
    green: 'bg-green-100 text-green-800',
    teal: 'bg-teal-100 text-teal-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    pink: 'bg-pink-100 text-pink-800',
};

export default function Badge({ color = 'gray', text, className = '' }) {
    const classes = COLOR_CLASSES[color] ?? COLOR_CLASSES.gray;
    return (
        <span
            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${classes} ${className}`}
        >
            {text}
        </span>
    );
}
