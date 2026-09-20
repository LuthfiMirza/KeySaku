import './bootstrap';
import './chart';
import { formatMoney } from './money';
window.formatMoney = formatMoney;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
