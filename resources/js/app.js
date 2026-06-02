import './bootstrap';
import {
    Banknote,
    Bell,
    Car,
    ChartColumnIncreasing,
    CircleDot,
    ChevronRight,
    Coins,
    createIcons,
    Gamepad2,
    Gift,
    Globe,
    GraduationCap,
    HeartPulse,
    House,
    Lock,
    MoreHorizontal,
    PiggyBank,
    Plus,
    Receipt,
    Settings,
    ShoppingCart,
    Smartphone,
    Tag,
    User,
    Utensils,
} from 'lucide';
const icons = {
    Banknote,
    Bell,
    Car,
    ChartColumnIncreasing,
    CircleDot,
    ChevronRight,
    Coins,
    Gamepad2,
    Gift,
    Globe,
    GraduationCap,
    HeartPulse,
    House,
    Lock,
    MoreHorizontal,
    PiggyBank,
    Plus,
    Receipt,
    Settings,
    ShoppingCart,
    Smartphone,
    Tag,
    User,
    Utensils,
};

const renderLucideIcons = () => {
    createIcons({ icons });
};

document.addEventListener('DOMContentLoaded', renderLucideIcons);
document.addEventListener('livewire:navigated', renderLucideIcons);
document.addEventListener('livewire:updated', renderLucideIcons);
