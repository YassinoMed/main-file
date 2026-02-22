/**
 * ERPGo SaaS - UX Enhancements
 * Global Search, Notifications, Favorites, Clock Timer, Onboarding,
 * Batch Actions, Dark Mode, Scroll-to-top, Keyboard Shortcuts, PWA
 */

(function () {
    'use strict';

    // ==========================================
    // 1. GLOBAL SEARCH (Cmd+K / Ctrl+K)
    // ==========================================
    const searchModules = [
        { name: 'Dashboard', url: '/dashboard', icon: 'ti ti-dashboard', cat: 'Navigation', cls: 'icon-hr' },
        { name: 'Employees', url: '/employee', icon: 'ti ti-users', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Attendance', url: '/attendanceemployee', icon: 'ti ti-clock', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Leave Management', url: '/leave', icon: 'ti ti-calendar-off', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Payslip', url: '/payslip', icon: 'ti ti-report-money', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Holidays', url: '/holiday', icon: 'ti ti-beach', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Meetings', url: '/meeting', icon: 'ti ti-video', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Awards', url: '/award', icon: 'ti ti-trophy', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Training', url: '/training', icon: 'ti ti-school', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Jobs', url: '/job', icon: 'ti ti-briefcase', cat: 'HRM', cls: 'icon-hr' },
        { name: 'Invoices', url: '/invoice', icon: 'ti ti-file-invoice', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Bills', url: '/bill', icon: 'ti ti-receipt', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Expenses', url: '/expense', icon: 'ti ti-credit-card', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Revenue', url: '/revenue', icon: 'ti ti-trending-up', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Bank Accounts', url: '/bank-account', icon: 'ti ti-building-bank', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Taxes', url: '/taxes', icon: 'ti ti-percentage', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Proposals', url: '/proposal', icon: 'ti ti-file-text', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Projects', url: '/projects', icon: 'ti ti-folder', cat: 'Project', cls: 'icon-project' },
        { name: 'Tasks', url: '/tasks', icon: 'ti ti-checkbox', cat: 'Project', cls: 'icon-project' },
        { name: 'Time Tracker', url: '/time-tracker', icon: 'ti ti-clock', cat: 'Project', cls: 'icon-project' },
        { name: 'Leads', url: '/leads', icon: 'ti ti-magnet', cat: 'CRM', cls: 'icon-crm' },
        { name: 'Deals', url: '/deals', icon: 'ti ti-handshake', cat: 'CRM', cls: 'icon-crm' },
        { name: 'Clients', url: '/clients', icon: 'ti ti-address-book', cat: 'CRM', cls: 'icon-crm' },
        { name: 'Contracts', url: '/contract', icon: 'ti ti-file-check', cat: 'CRM', cls: 'icon-crm' },
        { name: 'Customers', url: '/customer', icon: 'ti ti-user-check', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Vendors', url: '/vender', icon: 'ti ti-truck', cat: 'Finance', cls: 'icon-finance' },
        { name: 'Products & Services', url: '/productservice', icon: 'ti ti-package', cat: 'Finance', cls: 'icon-finance' },
        { name: 'POS', url: '/pos', icon: 'ti ti-device-tablet', cat: 'POS', cls: 'icon-pos' },
        { name: 'Settings', url: '/settings', icon: 'ti ti-settings', cat: 'System', cls: 'icon-settings' },
        { name: 'Users', url: '/users', icon: 'ti ti-user-plus', cat: 'System', cls: 'icon-settings' },
        { name: 'Roles', url: '/roles', icon: 'ti ti-shield', cat: 'System', cls: 'icon-settings' },
        { name: 'Reports', url: '/report', icon: 'ti ti-chart-bar', cat: 'Finance', cls: 'icon-finance' },
    ];

    function getMetaContent(name) {
        var el = document.querySelector('meta[name="' + name + '"]');
        return el ? (el.getAttribute('content') || '') : '';
    }

    function sameOrigin(url) {
        try {
            var u = new URL(url, window.location.href);
            return u.origin === window.location.origin;
        } catch (e) {
            return false;
        }
    }

    function safePathname(url) {
        try {
            return new URL(url, window.location.href).pathname || '';
        } catch (e) {
            return '';
        }
    }

    function nowTs() {
        return Date.now ? Date.now() : (new Date()).getTime();
    }

    function readJsonStorage(key, fallback) {
        try {
            var raw = localStorage.getItem(key);
            if (!raw) return fallback;
            var parsed = JSON.parse(raw);
            return parsed == null ? fallback : parsed;
        } catch (e) {
            return fallback;
        }
    }

    function writeJsonStorage(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            return false;
        }
    }

    function getRecentTypeFromPath(pathname) {
        var p = String(pathname || '');
        if (/^\/invoice(\/|$)/.test(p)) return 'invoice';
        if (/^\/projects(\/|$)/.test(p)) return 'project';
        if (/^\/leads(\/|$)/.test(p)) return 'lead';
        if (/^\/employee(\/|$)/.test(p)) return 'employee';
        if (/^\/clients(\/|$)/.test(p)) return 'client';
        return 'page';
    }

    function addLocalRecent(item) {
        if (!item || !item.visit_url) return;
        var list = readJsonStorage('erpgo_recent_items', []);
        if (!Array.isArray(list)) list = [];

        var deduped = [];
        for (var i = 0; i < list.length; i++) {
            var existing = list[i];
            if (!existing || !existing.visit_url) continue;
            if (String(existing.visit_url) === String(item.visit_url)) continue;
            deduped.push(existing);
        }

        item.ts = nowTs();
        deduped.unshift(item);

        writeJsonStorage('erpgo_recent_items', deduped.slice(0, 15));
    }

    function getLocalRecents() {
        var list = readJsonStorage('erpgo_recent_items', []);
        if (!Array.isArray(list)) return [];
        return list.slice(0, 12).map(function (it) {
            return {
                type: it.type || 'page',
                title: it.title || '',
                subtitle: it.subtitle || null,
                visit_url: it.visit_url || '#',
                ts: it.ts || 0,
            };
        }).filter(function (it) { return it.title && it.visit_url; });
    }

    function mergeRecents(localItems, serverItems) {
        var merged = [];
        var seen = {};

        (localItems || []).concat(serverItems || []).forEach(function (it) {
            if (!it || !it.visit_url) return;
            var key = String(it.visit_url);
            if (seen[key]) return;
            seen[key] = true;
            merged.push(it);
        });

        merged.sort(function (a, b) {
            var ta = parseInt(a.ts || 0, 10) || 0;
            var tb = parseInt(b.ts || 0, 10) || 0;
            return tb - ta;
        });

        return merged.slice(0, 12);
    }

    function initGlobalSearch() {
        if (document.getElementById('globalSearchOverlay')) {
            globalSearchServerUrl = getMetaContent('global-search-url') || '/global-search';
            return;
        }

        var overlay = document.createElement('div');
        overlay.className = 'global-search-overlay';
        overlay.id = 'globalSearchOverlay';

        var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        var defaultPlaceholder = lang.indexOf('fr') === 0 ? 'Rechercher…' : 'Search…';
        var placeholder = getMetaContent('global-search-placeholder') || defaultPlaceholder;

        overlay.innerHTML = '<div class="global-search-container" id="globalSearchContainer">' +
            '<div class="global-search-input-wrap">' +
            '<i class="ti ti-search"></i>' +
            '<input type="text" id="globalSearchInput" placeholder="' + placeholder + '" autocomplete="off">' +
            '<span class="search-shortcut">ESC</span></div>' +
            '<div class="global-search-results" id="globalSearchResults"></div>' +
            '<div class="global-search-footer"><span>Navigate with <kbd>↑</kbd><kbd>↓</kbd> • Open <kbd>↵</kbd></span>' +
            '<span><kbd>/</kbd> search • <kbd>N</kbd> new</span></div></div>';
        document.body.appendChild(overlay);

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeSearch();
        });

        overlay.addEventListener('click', function (e) {
            var target = e.target;
            var item = target && target.closest ? target.closest('.search-result-item') : null;
            if (!item) return;
            var action = item.getAttribute('data-action');
            if (!action) return;
            e.preventDefault();
            executeCommand(action);
        });

        var input = document.getElementById('globalSearchInput');
        input.addEventListener('input', function () { filterResults(this.value); });
        input.addEventListener('keydown', handleSearchNav);

        document.addEventListener('keydown', function (e) {
            var isK = (e.key || '').toLowerCase() === 'k';
            if ((e.metaKey || e.ctrlKey) && isK) {
                e.preventDefault();
                openSearch();
            }
            if (e.key === 'Escape') closeSearch();
        });

        globalSearchServerUrl = getMetaContent('global-search-url') || '/global-search';
        filterResults('');
    }

    var activeIdx = -1;
    var globalSearchPayload = null;
    var globalSearchLoading = false;
    var globalSearchDebounce = null;
    var globalSearchRequestSeq = 0;
    var globalSearchServerUrl = '';
    var globalSearchQueryContext = null;

    function openSearch() {
        var o = document.getElementById('globalSearchOverlay');
        o.classList.add('active');
        document.getElementById('globalSearchInput').value = '';
        globalSearchQueryContext = null;
        filterResults('');
        setTimeout(function () { document.getElementById('globalSearchInput').focus(); }, 100);
    }

    function closeSearch() {
        document.getElementById('globalSearchOverlay').classList.remove('active');
        activeIdx = -1;
        globalSearchQueryContext = null;
    }

    window.openSearch = openSearch;

    function escapeHtml(value) {
        var str = value == null ? '' : String(value);
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function labelFor(lang, fr, en) {
        return (lang || '').indexOf('fr') === 0 ? fr : en;
    }

    function iconForType(type) {
        var t = String(type || '').toLowerCase();
        if (t === 'client') return { icon: 'ti ti-address-book', cls: 'icon-crm' };
        if (t === 'invoice') return { icon: 'ti ti-file-invoice', cls: 'icon-finance' };
        if (t === 'project') return { icon: 'ti ti-folder', cls: 'icon-project' };
        if (t === 'employee') return { icon: 'ti ti-users', cls: 'icon-hr' };
        if (t === 'lead') return { icon: 'ti ti-magnet', cls: 'icon-crm' };
        return { icon: 'ti ti-search', cls: 'icon-action' };
    }

    function highlightMatch(text, q) {
        var str = text == null ? '' : String(text);
        var query = (q || '').trim();
        if (!query || query.length < 2) return escapeHtml(str);

        var idx = str.toLowerCase().indexOf(query.toLowerCase());
        if (idx < 0) return escapeHtml(str);

        var before = str.slice(0, idx);
        var match = str.slice(idx, idx + query.length);
        var after = str.slice(idx + query.length);
        return escapeHtml(before) + '<mark>' + escapeHtml(match) + '</mark>' + escapeHtml(after);
    }

    function renderResultItem(title, subtitle, url, icon, cls) {
        var safeTitle = highlightMatch(title, globalSearchLastQuery || '');
        var safeSubtitle = subtitle ? highlightMatch(subtitle, globalSearchLastQuery || '') : '';
        return '<a href="' + escapeHtml(url) + '" class="search-result-item" data-url="' + escapeHtml(url) + '">' +
            '<div class="result-icon ' + cls + '"><i class="' + icon + '"></i></div>' +
            '<div class="result-info"><div class="result-title">' + safeTitle + '</div>' +
            (safeSubtitle ? '<div class="result-desc">' + safeSubtitle + '</div>' : '') +
            '</div>' +
            '<i class="ti ti-arrow-right result-arrow"></i></a>';
    }

    function renderActionItem(title, subtitle, actionId, icon, cls) {
        var safeTitle = escapeHtml(title);
        var safeSubtitle = subtitle ? escapeHtml(subtitle) : '';
        return '<a href="#" class="search-result-item" data-action="' + escapeHtml(actionId) + '">' +
            '<div class="result-icon ' + cls + '"><i class="' + icon + '"></i></div>' +
            '<div class="result-info"><div class="result-title">' + safeTitle + '</div>' +
            (safeSubtitle ? '<div class="result-desc">' + safeSubtitle + '</div>' : '') +
            '</div>' +
            '<i class="ti ti-arrow-right result-arrow"></i></a>';
    }

    function renderCombined(q, modulesFiltered) {
        var res = document.getElementById('globalSearchResults');
        var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        var qTrim = (q || '').trim();
        var qLen = qTrim.length;
        var qLower = qTrim.toLowerCase();
        var wantsCommands = qTrim.indexOf('>') === 0;
        var qCmd = wantsCommands ? qTrim.slice(1).trim().toLowerCase() : qLower;

        var html = '';

        var commands = [
            {
                id: 'go_invoice_create',
                name: labelFor(lang, 'Créer une facture', 'Create invoice'),
                desc: 'invoice/create',
                icon: 'ti ti-file-plus',
                cls: 'icon-finance',
                match: ['invoice', 'facture', 'create', 'nouvelle', 'new'],
                url: '/invoice/create'
            },
            {
                id: 'go_pos_create',
                name: labelFor(lang, 'Nouvelle vente POS', 'New POS sale'),
                desc: 'pos/create',
                icon: 'ti ti-device-tablet-plus',
                cls: 'icon-pos',
                match: ['pos', 'sale', 'vente', 'caisse', 'new'],
                url: '/pos/create'
            },
            {
                id: 'go_lead_create',
                name: labelFor(lang, 'Créer un lead', 'Create lead'),
                desc: 'leads/create',
                icon: 'ti ti-magnet',
                cls: 'icon-crm',
                match: ['lead', 'crm', 'create', 'nouveau', 'new'],
                url: '/leads/create'
            },
            {
                id: 'go_project_create',
                name: labelFor(lang, 'Créer un projet', 'Create project'),
                desc: 'projects/create',
                icon: 'ti ti-folder-plus',
                cls: 'icon-project',
                match: ['project', 'projet', 'create', 'nouveau', 'new'],
                url: '/projects/create'
            },
            {
                id: 'go_proposal_create',
                name: labelFor(lang, 'Créer un devis', 'Create proposal'),
                desc: 'proposal/create',
                icon: 'ti ti-file-text',
                cls: 'icon-finance',
                match: ['proposal', 'devis', 'create', 'nouveau', 'new'],
                url: '/proposal/create'
            },
            {
                id: 'open_notifications',
                name: labelFor(lang, 'Ouvrir les notifications', 'Open notifications'),
                desc: labelFor(lang, 'Centre de notifications', 'Notification center'),
                icon: 'ti ti-bell',
                cls: 'icon-action',
                match: ['notif', 'notification', 'bell', 'alert', 'alerte']
            },
            {
                id: 'mark_all_notifications_read',
                name: labelFor(lang, 'Marquer toutes les notifications lues', 'Mark all notifications read'),
                desc: labelFor(lang, 'Nettoyer les alertes', 'Clear alerts'),
                icon: 'ti ti-check',
                cls: 'icon-action',
                match: ['notif', 'notification', 'read', 'lu', 'all', 'toutes', 'mark']
            }
        ];

        var commandFiltered = commands.filter(function (c) {
            if (!qLen) return true;
            var hay = [c.name, c.desc || ''].concat(c.match || []).join(' ').toLowerCase();
            return hay.indexOf(qCmd) > -1;
        });

        if ((wantsCommands && commandFiltered.length) || (!wantsCommands && qLen < 2 && commandFiltered.length)) {
            html += '<div class="search-result-group"><div class="search-result-group-title">' +
                labelFor(lang, 'Actions', 'Actions') + '</div>';
            commandFiltered.slice(0, 8).forEach(function (c) {
                if (c.url) {
                    html += renderResultItem(c.name, c.desc, c.url, c.icon, c.cls);
                } else {
                    html += renderActionItem(c.name, c.desc, c.id, c.icon, c.cls);
                }
            });
            html += '</div>';
        }

        if (qLen < 2) {
            var serverRecent = (globalSearchPayload && globalSearchPayload.recent && globalSearchPayload.recent.length) ? globalSearchPayload.recent : [];
            var localRecent = getLocalRecents();
            var merged = mergeRecents(localRecent, serverRecent);
            if (merged.length) {
            html += '<div class="search-result-group"><div class="search-result-group-title">' +
                labelFor(lang, 'Récents', 'Recent') + '</div>';
                merged.forEach(function (item) {
                var iconMeta = iconForType(item.type);
                html += renderResultItem(item.title, item.subtitle, item.visit_url, iconMeta.icon, iconMeta.cls);
            });
            html += '</div>';
            }
        }

        if (globalSearchQueryContext && globalSearchQueryContext.type === 'create_invoice_for_client') {
            var clients = (globalSearchPayload && globalSearchPayload.results && globalSearchPayload.results.clients) ? globalSearchPayload.results.clients : [];
            if (clients.length) {
                html += '<div class="search-result-group"><div class="search-result-group-title">' +
                    labelFor(lang, 'Créer', 'Create') + '</div>';
                clients.slice(0, 8).forEach(function (client) {
                    if (!client || !client.id || !client.title) return;
                    var url = '/invoice/create/' + encodeURIComponent(client.id);
                    html += renderResultItem(labelFor(lang, 'Facture pour ', 'Invoice for ') + client.title, client.subtitle || 'invoice/create', url, 'ti ti-file-plus', 'icon-finance');
                });
                html += '</div>';
            }
        }

        if (globalSearchQueryContext && globalSearchQueryContext.type === 'create_proposal_for_client') {
            var clients2 = (globalSearchPayload && globalSearchPayload.results && globalSearchPayload.results.clients) ? globalSearchPayload.results.clients : [];
            if (clients2.length) {
                html += '<div class="search-result-group"><div class="search-result-group-title">' +
                    labelFor(lang, 'Créer', 'Create') + '</div>';
                clients2.slice(0, 8).forEach(function (client) {
                    if (!client || !client.id || !client.title) return;
                    var url = '/proposal/create/' + encodeURIComponent(client.id);
                    html += renderResultItem(labelFor(lang, 'Devis pour ', 'Proposal for ') + client.title, client.subtitle || 'proposal/create', url, 'ti ti-file-text', 'icon-finance');
                });
                html += '</div>';
            }
        }

        if (modulesFiltered.length) {
            var groups = {};
            modulesFiltered.forEach(function (m) {
                if (!groups[m.cat]) groups[m.cat] = [];
                groups[m.cat].push(m);
            });
            Object.keys(groups).forEach(function (cat) {
                html += '<div class="search-result-group"><div class="search-result-group-title">' + escapeHtml(cat) + '</div>';
                groups[cat].forEach(function (m) {
                    html += renderResultItem(m.name, m.cat, m.url, m.icon, m.cls);
                });
                html += '</div>';
            });
        }

        if (qLen >= 2) {
            var sections = [
                { key: 'clients', type: 'client', title: labelFor(lang, 'Clients', 'Clients') },
                { key: 'invoices', type: 'invoice', title: labelFor(lang, 'Factures', 'Invoices') },
                { key: 'projects', type: 'project', title: labelFor(lang, 'Projets', 'Projects') },
                { key: 'employees', type: 'employee', title: labelFor(lang, 'Employés', 'Employees') },
                { key: 'leads', type: 'lead', title: labelFor(lang, 'Leads', 'Leads') },
            ];

            if (globalSearchPayload && globalSearchPayload.results) {
                sections.forEach(function (section) {
                    var items = globalSearchPayload.results[section.key] || [];
                    if (!items.length) return;
                    html += '<div class="search-result-group"><div class="search-result-group-title">' + escapeHtml(section.title) + '</div>';
                    var iconMeta = iconForType(section.type);
                    items.forEach(function (item) {
                        html += renderResultItem(item.title, item.subtitle, item.visit_url, iconMeta.icon, iconMeta.cls);
                    });
                    html += '</div>';
                });
            }
        }

        if (!html && globalSearchLoading) {
            html = '<div class="search-no-results"><i class="ti ti-loader"></i><p>' +
                labelFor(lang, 'Chargement…', 'Loading…') + '</p></div>';
        }

        if (!html) {
            if (qLen === 0) {
                html = '<div class="search-no-results"><i class="ti ti-search"></i><p>' +
                    labelFor(lang, 'Commencez à taper pour rechercher…', 'Start typing to search…') +
                    '</p></div>';
            } else {
                html = '<div class="search-no-results"><i class="ti ti-search-off"></i><p>' +
                    labelFor(lang, 'Aucun résultat pour "', 'No results for "') + escapeHtml(qTrim) + '"</p></div>';
            }
        }

        res.innerHTML = html;
        activeIdx = -1;
    }

    function scheduleServerFetch(q) {
        var qTrim = (q || '').trim();
        var qToSend = qTrim.length >= 2 ? qTrim : '';

        globalSearchQueryContext = null;
        if (/^(facture pour|invoice for)\s+/i.test(qTrim)) {
            globalSearchQueryContext = { type: 'create_invoice_for_client' };
            qToSend = qTrim.replace(/^(facture pour|invoice for)\s+/i, '').trim();
        } else if (/^(devis pour|proposal for)\s+/i.test(qTrim)) {
            globalSearchQueryContext = { type: 'create_proposal_for_client' };
            qToSend = qTrim.replace(/^(devis pour|proposal for)\s+/i, '').trim();
        }

        if (!globalSearchServerUrl) {
            globalSearchServerUrl = getMetaContent('global-search-url') || '/global-search';
        }

        if (globalSearchDebounce) {
            clearTimeout(globalSearchDebounce);
        }

        globalSearchDebounce = setTimeout(function () {
            fetchServer(qToSend);
        }, 200);
    }

    function fetchServer(q) {
        var requestId = ++globalSearchRequestSeq;
        globalSearchLoading = true;
        var input = document.getElementById('globalSearchInput');
        var currentQ = input ? (input.value || '') : (q || '');
        var currentFiltered = searchModules.filter(function (m) {
            return m.name.toLowerCase().indexOf(currentQ.toLowerCase()) > -1 ||
                m.cat.toLowerCase().indexOf(currentQ.toLowerCase()) > -1;
        });
        renderCombined(currentQ, currentFiltered);

        var url = globalSearchServerUrl;
        var sep = url.indexOf('?') === -1 ? '?' : '&';
        var fullUrl = url + sep + 'q=' + encodeURIComponent(q || '') + '&limit=5';

        fetch(fullUrl, { method: 'GET', headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) {
                if (!r.ok) throw new Error('bad response');
                return r.json();
            })
            .then(function (payload) {
                if (requestId !== globalSearchRequestSeq) return;
                globalSearchPayload = payload || null;
                globalSearchLoading = false;
                if (globalSearchPayload && globalSearchPayload.recent && Array.isArray(globalSearchPayload.recent)) {
                    globalSearchPayload.recent = globalSearchPayload.recent.map(function (it) {
                        it.ts = it.ts || 0;
                        return it;
                    });
                }
                var input = document.getElementById('globalSearchInput');
                var currentQ = input ? (input.value || '') : '';
                var currentFiltered = searchModules.filter(function (m) {
                    return m.name.toLowerCase().indexOf(currentQ.toLowerCase()) > -1 ||
                        m.cat.toLowerCase().indexOf(currentQ.toLowerCase()) > -1;
                });
                renderCombined(currentQ, currentFiltered);
            })
            .catch(function () {
                if (requestId !== globalSearchRequestSeq) return;
                globalSearchPayload = null;
                globalSearchLoading = false;
                var input = document.getElementById('globalSearchInput');
                var currentQ = input ? (input.value || '') : '';
                var currentFiltered = searchModules.filter(function (m) {
                    return m.name.toLowerCase().indexOf(currentQ.toLowerCase()) > -1 ||
                        m.cat.toLowerCase().indexOf(currentQ.toLowerCase()) > -1;
                });
                renderCombined(currentQ, currentFiltered);
            });
    }

    function filterResults(q) {
        var qTrim = (q || '').trim();
        globalSearchLastQuery = qTrim;
        var filtered = searchModules.filter(function (m) {
            return m.name.toLowerCase().indexOf(qTrim.toLowerCase()) > -1 ||
                m.cat.toLowerCase().indexOf(qTrim.toLowerCase()) > -1;
        });
        renderCombined(qTrim, filtered);
        scheduleServerFetch(qTrim);
    }

    var globalSearchLastQuery = '';

    function handleSearchNav(e) {
        var items = document.querySelectorAll('.search-result-item');
        if (e.key === 'ArrowDown') { e.preventDefault(); activeIdx = Math.min(activeIdx + 1, items.length - 1); }
        if (e.key === 'ArrowUp') { e.preventDefault(); activeIdx = Math.max(activeIdx - 1, 0); }
        items.forEach(function (el, i) { el.classList.toggle('active', i === activeIdx); });
        if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && activeIdx >= 0 && items[activeIdx] && items[activeIdx].scrollIntoView) {
            items[activeIdx].scrollIntoView({ block: 'nearest' });
        }
        if (e.key === 'Enter' && activeIdx >= 0 && items[activeIdx]) {
            var action = items[activeIdx].getAttribute('data-action');
            if (action) {
                executeCommand(action);
                return;
            }
            window.location.href = items[activeIdx].getAttribute('href');
        }
    }

    function executeCommand(id) {
        if (id === 'open_notifications') {
            closeSearch();
            if (window.toggleNotifPanel) window.toggleNotifPanel();
            return;
        }
        if (id === 'mark_all_notifications_read') {
            closeSearch();
            if (window.__markAllNotificationsRead) window.__markAllNotificationsRead();
            return;
        }
    }

    // ==========================================
    // 2. NOTIFICATION CENTER
    // ==========================================
    function initNotificationCenter() {
        var panelUrl = getMetaContent('notifications-panel-url') || '/notifications/panel';
        var readAllUrl = getMetaContent('notifications-read-all-url') || '/notifications/read-all';
        var readUrlTemplate = getMetaContent('notifications-read-url-template') || '/notifications/0/read';
        var auditFeedUrl = getMetaContent('audit-log-feed-url') || '/audit-log/feed';
        var auditExportCsvUrl = getMetaContent('audit-log-export-csv-url') || '/audit-log/export/csv';
        var cacheItems = [];
        var cacheAudit = [];
        var activeTab = 'all';

        var panel = document.createElement('div');
        panel.className = 'notification-panel';
        panel.id = 'notificationPanel';
        panel.setAttribute('data-active-tab', activeTab);
        var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        panel.innerHTML = '<div class="notification-panel-header"><h5>' + labelFor(lang, 'Notifications', 'Notifications') + '</h5>' +
            '<button class="close-btn" onclick="document.getElementById(\'notificationPanel\').classList.remove(\'open\')">' +
            '<i class="ti ti-x"></i></button></div>' +
            '<div class="notification-tabs">' +
            '<div class="notification-tab active" data-tab="all">' + labelFor(lang, 'Tout', 'All') + '</div>' +
            '<div class="notification-tab" data-tab="unread">' + labelFor(lang, 'Non lus', 'Unread') + '</div>' +
            '<div class="notification-tab" data-tab="audit">' + labelFor(lang, 'Audit', 'Audit') + '</div></div>' +
            '<div class="notification-list" id="notifList"></div>' +
            '<div class="notification-panel-footer">' +
            '<a href="#" id="notifMarkAllRead">' + labelFor(lang, 'Tout marquer comme lu', 'Mark all as read') + '</a>' +
            '<a href="' + escapeHtml(auditExportCsvUrl) + '" id="auditExportCsv" style="display:none">' + labelFor(lang, 'Exporter CSV', 'Export CSV') + '</a>' +
            '</div>';
        document.body.appendChild(panel);

        function getCsrf() {
            var el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        }

        function notificationReadUrl(id) {
            return String(readUrlTemplate).replace(/0(?!.*0)/, String(id));
        }

        function setBadge(count) {
            var badge = document.querySelector('.notification-center-btn .notification-badge');
            if (!badge) return;
            var c = parseInt(count || 0, 10) || 0;
            badge.textContent = String(c);
            badge.style.display = c > 0 ? '' : 'none';
        }

        function render() {
            var list = panel.querySelector('#notifList');
            if (!list) return;

            if (activeTab === 'audit') {
                if (!cacheAudit.length) {
                    list.innerHTML = '<div class="search-no-results"><i class="ti ti-activity"></i><p>' +
                        labelFor(lang, 'Aucune activité', 'No activity') + '</p></div>';
                    return;
                }

                list.innerHTML = cacheAudit.map(function (item) {
                    return '<div class="notification-item">' +
                        '<div class="notif-icon notif-system"><i class="ti ' + escapeHtml(item.icon || 'ti-activity') + '"></i></div>' +
                        '<div class="notif-content">' +
                        '<div class="notif-title">' + (item.html || escapeHtml(item.title || '')) + '</div>' +
                        '<div class="notif-time">' + escapeHtml(item.time || '') + '</div>' +
                        '</div></div>';
                }).join('');
                return;
            }

            var filtered = cacheItems;
            if (activeTab === 'unread') {
                filtered = cacheItems.filter(function (i) { return parseInt(i.is_read || 0, 10) === 0; });
            }

            if (!filtered.length) {
                list.innerHTML = '<div class="search-no-results"><i class="ti ti-bell"></i><p>' +
                    labelFor(lang, 'Aucune notification', 'No notifications') + '</p></div>';
                return;
            }

            list.innerHTML = filtered.map(function (item) { return item.html || ''; }).join('');
        }

        function refresh() {
            return fetch(panelUrl, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            }).then(function (r) { return r.json(); }).then(function (payload) {
                cacheItems = (payload && payload.items) ? payload.items : [];
                setBadge(payload && payload.unread_count);
                render();
            }).catch(function () {
                render();
            });
        }

        function refreshAudit() {
            return fetch(auditFeedUrl, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            }).then(function (r) { return r.json(); }).then(function (payload) {
                cacheAudit = (payload && payload.items) ? payload.items : [];
                render();
            }).catch(function () {
                render();
            });
        }

        panel.querySelectorAll('.notification-tab').forEach(function (tab) {
            tab.addEventListener('click', function () {
                panel.querySelectorAll('.notification-tab').forEach(function (t) { t.classList.remove('active'); });
                this.classList.add('active');
                activeTab = this.getAttribute('data-tab') || 'all';
                panel.setAttribute('data-active-tab', activeTab);
                var footer = panel.querySelector('.notification-panel-footer');
                var exportCsv = panel.querySelector('#auditExportCsv');
                if (footer) footer.style.display = '';
                if (markAll) markAll.style.display = activeTab === 'audit' ? 'none' : '';
                if (exportCsv) exportCsv.style.display = activeTab === 'audit' ? '' : 'none';
                if (activeTab === 'audit') {
                    refreshAudit();
                    return;
                }
                render();
            });
        });

        var markAll = panel.querySelector('#notifMarkAllRead');
        if (markAll) {
            markAll.addEventListener('click', function (e) {
                e.preventDefault();
                fetch(readAllUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                    body: '{}'
                }).then(function () {
                    refresh();
                });
            });
        }

        window.__markAllNotificationsRead = function () {
            return fetch(readAllUrl, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrf(),
                },
                body: '{}'
            }).then(function () { return refresh(); });
        };

        var listEl = panel.querySelector('#notifList');
        if (listEl) {
            listEl.addEventListener('click', function (e) {
                var target = e.target;
                var wrapper = target && target.closest ? target.closest('[data-notification-id]') : null;
                if (!wrapper) return;
                var id = wrapper.getAttribute('data-notification-id');
                if (!id) return;
                fetch(notificationReadUrl(id), {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                    },
                    body: '{}'
                }).then(function () {
                    cacheItems = cacheItems.map(function (it) {
                        if (String(it.id) === String(id)) {
                            it.is_read = 1;
                        }
                        return it;
                    });
                    render();
                    var unreadCount = cacheItems.filter(function (i) { return parseInt(i.is_read || 0, 10) === 0; }).length;
                    setBadge(unreadCount);
                });
            });
        }

        window.__refreshNotifications = refresh;
        window.__refreshAudit = refreshAudit;
        refresh();
    }

    window.toggleNotifPanel = function () {
        var panel = document.getElementById('notificationPanel');
        if (!panel) return;
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            if (panel.getAttribute('data-active-tab') === 'audit' && window.__refreshAudit) {
                window.__refreshAudit();
            } else if (window.__refreshNotifications) {
                window.__refreshNotifications();
            }
        }
    };

    // ==========================================
    // 3. FAVORITES BAR
    // ==========================================
    function initFavoritesBar() {
        var defaults = JSON.parse(localStorage.getItem('erpgo_favorites') || '[]');
        if (defaults.length === 0) {
            defaults = [
                { name: 'Invoices', url: '/invoice', icon: 'ti ti-file-invoice' },
                { name: 'Employees', url: '/employee', icon: 'ti ti-users' },
                { name: 'Projects', url: '/projects', icon: 'ti ti-folder' },
            ];
            localStorage.setItem('erpgo_favorites', JSON.stringify(defaults));
        }
        renderFavorites(defaults);
    }

    function renderFavorites(favs) {
        var existing = document.querySelector('.favorites-bar');
        if (existing) existing.remove();

        var bar = document.createElement('div');
        bar.className = 'favorites-bar';
        bar.innerHTML = '<span class="fav-label">⭐ Quick Access:</span>';
        favs.forEach(function (f, i) {
            bar.innerHTML += '<a href="' + f.url + '" class="fav-item"><i class="' + f.icon + '"></i>' + f.name +
                '<span class="fav-remove" onclick="event.preventDefault();removeFav(' + i + ')">×</span></a>';
        });
        bar.innerHTML += '<div class="fav-add-btn" onclick="openSearch()" title="Add favorite"><i class="ti ti-plus"></i></div>';

        var container = document.querySelector('.dash-content');
        if (container) container.insertBefore(bar, container.firstChild);
    }

    window.removeFav = function (idx) {
        var favs = JSON.parse(localStorage.getItem('erpgo_favorites') || '[]');
        favs.splice(idx, 1);
        localStorage.setItem('erpgo_favorites', JSON.stringify(favs));
        renderFavorites(favs);
    };

    // ==========================================
    // 4. CLOCK TIMER IN HEADER
    // ==========================================
    function initClockTimer() {
        var headerList = document.querySelector('.header-wrapper .ms-auto .list-unstyled');
        if (!headerList) return;

        var li = document.createElement('li');
        li.className = 'dash-h-item clock-widget';
        li.innerHTML = '<div class="clock-timer-display" id="clockTimerDisplay">' +
            '<span class="timer-dot"></span><span id="clockTimerText">00:00:00</span></div>';
        headerList.insertBefore(li, headerList.firstChild);

        // Simple timer simulation
        var startTime = sessionStorage.getItem('erpgo_clock_start');
        if (startTime) {
            setInterval(function () { updateClockDisplay(parseInt(startTime)); }, 1000);
        }
    }

    function updateClockDisplay(start) {
        var elapsed = Math.floor((Date.now() - start) / 1000);
        var h = String(Math.floor(elapsed / 3600)).padStart(2, '0');
        var m = String(Math.floor((elapsed % 3600) / 60)).padStart(2, '0');
        var s = String(elapsed % 60).padStart(2, '0');
        var el = document.getElementById('clockTimerText');
        if (el) el.textContent = h + ':' + m + ':' + s;
    }

    // ==========================================
    // 5. DARK MODE TOGGLE
    // ==========================================
    function initDarkMode() {
        var headerList = document.querySelector('.header-wrapper .ms-auto .list-unstyled');
        if (!headerList) return;

        var stored = localStorage.getItem('erpgo_darkmode');
        var autoSetting = getMetaContent('dark-layout-auto') === 'on';
        var serverSetting = getMetaContent('dark-layout-setting') === 'on';
        var prefersDark = false;
        try {
            prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        } catch (e) { prefersDark = false; }
        var isDark = stored ? (stored === 'on') : (autoSetting ? prefersDark : serverSetting);
        var li = document.createElement('li');
        li.className = 'dash-h-item';
        li.innerHTML = '<button class="dark-mode-toggle ' + (isDark ? 'active' : '') + '" id="darkModeToggle" title="Toggle dark mode">' +
            '<div class="toggle-thumb"><span class="icon-sun">☀️</span><span class="icon-moon">🌙</span></div></button>';
        headerList.insertBefore(li, headerList.firstChild);

        function applyDarkMode(on) {
            var mainStyle = document.getElementById('main-style-link');
            if (mainStyle) {
                var lightHref = mainStyle.getAttribute('data-light');
                var darkHref = mainStyle.getAttribute('data-dark');
                if (darkHref && lightHref) {
                    mainStyle.setAttribute('href', on ? darkHref : lightHref);
                }
            }
            var customDark = document.getElementById('custom-dark-link');
            if (customDark) {
                customDark.disabled = !on;
            }
            document.body.classList.toggle('dark-mode', on);
            var toggle = document.getElementById('darkModeToggle');
            if (toggle) {
                toggle.classList.toggle('active', on);
            }
        }

        document.getElementById('darkModeToggle').addEventListener('click', function () {
            this.classList.toggle('active');
            var on = this.classList.contains('active');
            localStorage.setItem('erpgo_darkmode', on ? 'on' : 'off');
            applyDarkMode(on);
        });

        if (autoSetting && !stored && window.matchMedia) {
            var darkMedia = window.matchMedia('(prefers-color-scheme: dark)');
            var onChange = function (e) {
                applyDarkMode(e.matches);
            };
            if (darkMedia.addEventListener) {
                darkMedia.addEventListener('change', onChange);
            } else if (darkMedia.addListener) {
                darkMedia.addListener(onChange);
            }
        }

        applyDarkMode(isDark);
    }

    // ==========================================
    // 6. NOTIFICATION BELL IN HEADER
    // ==========================================
    function initNotifBell() {
        var headerList = document.querySelector('.header-wrapper .ms-auto .list-unstyled');
        if (!headerList) return;

        var li = document.createElement('li');
        li.className = 'dash-h-item notification-center-btn';
        li.innerHTML = '<a class="dash-head-link" href="javascript:void(0)" onclick="toggleNotifPanel()">' +
            '<i class="ti ti-bell"></i><span class="notification-badge" style="display:none">0</span></a>';

        var langDrop = headerList.querySelector('.drp-language');
        if (langDrop) headerList.insertBefore(li, langDrop);

        if (window.__refreshNotifications) {
            window.__refreshNotifications();
        }
    }

    // ==========================================
    // 7. SCROLL TO TOP
    // ==========================================
    function initScrollToTop() {
        var btn = document.createElement('button');
        btn.className = 'scroll-to-top';
        btn.id = 'scrollToTop';
        btn.innerHTML = '<i class="ti ti-arrow-up"></i>';
        btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
        document.body.appendChild(btn);

        window.addEventListener('scroll', function () {
            btn.classList.toggle('visible', window.scrollY > 300);
        });
    }

    // ==========================================
    // 8. BATCH ACTIONS
    // ==========================================
    function initBatchActions() {
        var toolbar = document.createElement('div');
        toolbar.className = 'batch-toolbar';
        toolbar.id = 'batchToolbar';
        toolbar.innerHTML = '<span class="batch-count"><span id="batchCount">0</span> selected</span>' +
            '<div class="batch-actions">' +
            '<button class="batch-action-btn" onclick="batchExport()"><i class="ti ti-download"></i> Export</button>' +
            '<button class="batch-action-btn danger" onclick="batchDelete()"><i class="ti ti-trash"></i> Delete</button></div>' +
            '<button class="batch-close" onclick="clearBatch()"><i class="ti ti-x"></i></button>';
        document.body.appendChild(toolbar);

        // Add checkboxes to dataTable rows
        setTimeout(function () {
            document.querySelectorAll('.dataTable-table thead tr').forEach(function (r) {
                var th = document.createElement('th');
                th.innerHTML = '<input type="checkbox" class="batch-checkbox" onchange="toggleAllBatch(this)">';
                th.style.width = '40px';
                r.insertBefore(th, r.firstChild);
            });
            document.querySelectorAll('.dataTable-table tbody tr').forEach(function (r) {
                var td = document.createElement('td');
                td.innerHTML = '<input type="checkbox" class="batch-checkbox" onchange="updateBatchCount()">';
                r.insertBefore(td, r.firstChild);
            });
        }, 2000);
    }

    function getSelectedBulkIds() {
        var ids = [];
        document.querySelectorAll('.dataTable-table tbody tr').forEach(function (tr) {
            var cb = tr.querySelector('td .batch-checkbox');
            if (!cb || !cb.checked) return;
            var id = tr.getAttribute('data-bulk-id') || tr.getAttribute('data-id');
            if (!id) return;
            ids.push(String(id));
        });
        return ids;
    }

    function getCsrfToken() {
        var el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute('content') : '';
    }

    function bulkEndpointForPage() {
        var p = window.location.pathname || '';
        if (/^\/invoice(\/|$)/.test(p)) return { url: '/invoice/bulk', kind: 'invoice' };
        if (/^\/leads\/list(\/|$)/.test(p)) return { url: '/leads/bulk', kind: 'lead' };
        return null;
    }

    window.toggleAllBatch = function (el) {
        document.querySelectorAll('.dataTable-table tbody .batch-checkbox').forEach(function (cb) { cb.checked = el.checked; });
        updateBatchCount();
    };
    window.updateBatchCount = function () {
        var count = document.querySelectorAll('.dataTable-table tbody .batch-checkbox:checked').length;
        var el = document.getElementById('batchCount');
        if (el) el.textContent = count;
        document.getElementById('batchToolbar').classList.toggle('visible', count > 0);
    };
    window.clearBatch = function () {
        document.querySelectorAll('.batch-checkbox').forEach(function (cb) { cb.checked = false; });
        document.getElementById('batchToolbar').classList.remove('visible');
    };
    window.batchExport = function () {
        var endpoint = bulkEndpointForPage();
        if (!endpoint) return;
        var ids = getSelectedBulkIds();
        if (!ids.length) return;
        var url = endpoint.url + '?action=export&ids=' + encodeURIComponent(ids.join(','));
        window.location.href = url;
    };
    window.batchDelete = function () {
        var endpoint = bulkEndpointForPage();
        if (!endpoint) return;
        var ids = getSelectedBulkIds();
        if (!ids.length) return;
        if (!confirm('Delete selected items?')) return;
        fetch(endpoint.url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ action: 'delete', ids: ids })
        }).then(function (r) { return r.json().catch(function () { return null; }); })
            .then(function (payload) {
                if (payload && payload.ok) {
                    window.location.reload();
                    return;
                }
                if (payload && payload.message) {
                    if (window.show_toastr) window.show_toastr('error', payload.message);
                }
            });
    };

    // ==========================================
    // 9. KEYBOARD SHORTCUTS
    // ==========================================
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', function (e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === '?' && e.shiftKey) showShortcutsHelp();
            if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                e.preventDefault();
                openSearch();
                return;
            }
            if ((e.ctrlKey || e.metaKey) && (e.key || '').toLowerCase() === 'n') {
                e.preventDefault();
                window.location.href = '/invoice/create';
                return;
            }
            if ((e.ctrlKey || e.metaKey) && (e.key || '').toLowerCase() === 'f') {
                e.preventDefault();
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                return;
            }
            if ((e.key || '').toLowerCase() === 'n' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                e.preventDefault();
                openSearch();
                setTimeout(function () {
                    var input = document.getElementById('globalSearchInput');
                    if (input) {
                        input.value = '>';
                        filterResults('>');
                        input.focus();
                    }
                }, 120);
                return;
            }
            if (e.key === 'g' && !e.ctrlKey && !e.metaKey) {
                setTimeout(function () {
                    document.addEventListener('keydown', function handler(e2) {
                        document.removeEventListener('keydown', handler);
                        if (e2.key === 'd') window.location.href = '/dashboard';
                        if (e2.key === 'e') window.location.href = '/employee';
                        if (e2.key === 'i') window.location.href = '/invoice';
                        if (e2.key === 'p') window.location.href = '/projects';
                        if (e2.key === 'l') window.location.href = '/leads';
                    });
                }, 0);
            }
        });
    }

    function showShortcutsHelp() {
        var modal = document.getElementById('commonModal');
        if (!modal) return;
        var title = modal.querySelector('.modal-title');
        var body = modal.querySelector('.body') || modal.querySelector('.modal-body');
        if (title) title.textContent = '⌨️ Keyboard Shortcuts';
        if (body) body.innerHTML = '<div class="shortcuts-modal p-3">' +
            '<div class="shortcut-group"><h6>General</h6>' +
            '<div class="shortcut-item"><span>Global Search</span><div class="shortcut-keys"><kbd>Ctrl</kbd><kbd>K</kbd></div></div>' +
            '<div class="shortcut-item"><span>New Invoice</span><div class="shortcut-keys"><kbd>Ctrl</kbd><kbd>N</kbd></div></div>' +
            '<div class="shortcut-item"><span>Focus Mode</span><div class="shortcut-keys"><kbd>Ctrl</kbd><kbd>F</kbd></div></div>' +
            '<div class="shortcut-item"><span>Show Shortcuts</span><div class="shortcut-keys"><kbd>Shift</kbd><kbd>?</kbd></div></div></div>' +
            '<div class="shortcut-group"><h6>Navigation (press G then...)</h6>' +
            '<div class="shortcut-item"><span>Dashboard</span><div class="shortcut-keys"><kbd>G</kbd><kbd>D</kbd></div></div>' +
            '<div class="shortcut-item"><span>Employees</span><div class="shortcut-keys"><kbd>G</kbd><kbd>E</kbd></div></div>' +
            '<div class="shortcut-item"><span>Invoices</span><div class="shortcut-keys"><kbd>G</kbd><kbd>I</kbd></div></div>' +
            '<div class="shortcut-item"><span>Projects</span><div class="shortcut-keys"><kbd>G</kbd><kbd>P</kbd></div></div></div></div>';
        var bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    // ==========================================
    // 10. SEARCH TRIGGER BUTTON IN HEADER
    // ==========================================
    function initSearchButton() {
        var headerList = document.querySelector('.header-wrapper .ms-auto .list-unstyled');
        if (!headerList) return;
        if (document.querySelector('[data-global-search-trigger]')) return;

        var li = document.createElement('li');
        li.className = 'dash-h-item';
        li.innerHTML = '<a class="dash-head-link" href="javascript:void(0)" onclick="openSearch()" title="Search (Ctrl+K)">' +
            '<i class="ti ti-search"></i></a>';
        headerList.insertBefore(li, headerList.firstChild);
    }

    function initRecentMenu() {
        var headerList = document.querySelector('.header-wrapper .ms-auto .list-unstyled');
        if (!headerList) return;
        if (document.getElementById('recentMenuToggle')) return;

        var li = document.createElement('li');
        li.className = 'dropdown dash-h-item';
        li.innerHTML = '<a class="dash-head-link dropdown-toggle arrow-none me-0" href="#" id="recentMenuToggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
            '<i class="ti ti-history"></i></a>' +
            '<div class="dropdown-menu dash-h-dropdown dropdown-menu-end" style="min-width: 320px">' +
            '<div class="px-3 py-2 fw-semibold">' + ((document.documentElement.getAttribute('lang') || '').toLowerCase().indexOf('fr') === 0 ? 'Récents' : 'Recent') + '</div>' +
            '<div id="recentMenuItems" class="px-2 pb-2"></div></div>';

        headerList.insertBefore(li, headerList.firstChild);

        function render(items) {
            var box = document.getElementById('recentMenuItems');
            if (!box) return;
            if (!items || !items.length) {
                box.innerHTML = '<div class="text-muted px-2 py-2" style="font-size:12px">' +
                    ((document.documentElement.getAttribute('lang') || '').toLowerCase().indexOf('fr') === 0 ? 'Aucun récent' : 'No recent items') +
                    '</div>';
                return;
            }

            box.innerHTML = items.slice(0, 10).map(function (it) {
                var iconMeta = iconForType(it.type);
                var title = escapeHtml(it.title || '');
                var subtitle = it.subtitle ? escapeHtml(it.subtitle) : '';
                return '<a class="dropdown-item d-flex align-items-center gap-2" href="' + escapeHtml(it.visit_url) + '">' +
                    '<span class="badge bg-light text-dark"><i class="' + iconMeta.icon + '"></i></span>' +
                    '<span class="flex-grow-1 overflow-hidden"><span class="d-block text-truncate">' + title + '</span>' +
                    (subtitle ? '<span class="d-block text-muted text-truncate" style="font-size:12px">' + subtitle + '</span>' : '') +
                    '</span></a>';
            }).join('');
        }

        var dropdown = li.querySelector('.dropdown-menu');
        if (!dropdown) return;
        dropdown.addEventListener('show.bs.dropdown', function () {
            render(getLocalRecents());
            var serverUrl = getMetaContent('global-search-url') || '/global-search';
            var sep = serverUrl.indexOf('?') === -1 ? '?' : '&';
            fetch(serverUrl + sep + 'q=&limit=1', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (payload) {
                    var merged = mergeRecents(getLocalRecents(), (payload && payload.recent) ? payload.recent : []);
                    render(merged);
                })
                .catch(function () { });
        });
    }

    function initLocalRecentsTracking() {
        document.addEventListener('click', function (e) {
            var target = e.target;
            var a = target && target.closest ? target.closest('a') : null;
            if (!a) return;
            var href = a.getAttribute('href') || '';
            if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
            if (!sameOrigin(href)) return;

            var pathname = safePathname(href);
            if (!pathname || pathname === '/' || pathname === window.location.pathname) return;

            var title = (a.textContent || '').trim();
            if (!title) title = pathname;

            addLocalRecent({
                type: getRecentTypeFromPath(pathname),
                title: title.slice(0, 90),
                subtitle: null,
                visit_url: href,
            });
        }, true);
    }

    function initPrefetch() {
        var prefetched = {};
        function prefetch(url) {
            if (!url || !sameOrigin(url)) return;
            var u;
            try { u = new URL(url, window.location.href); } catch (e) { return; }
            if (u.pathname === window.location.pathname) return;
            var key = u.href;
            if (prefetched[key]) return;
            prefetched[key] = true;

            var link = document.createElement('link');
            link.rel = 'prefetch';
            link.as = 'document';
            link.href = u.href;
            document.head.appendChild(link);
        }

        var timer = null;
        document.addEventListener('mouseover', function (e) {
            var a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (!a) return;
            var href = a.getAttribute('href') || '';
            if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
            if (!sameOrigin(href)) return;
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () { prefetch(href); }, 120);
        }, { passive: true });
    }

    function initFormAutosave() {
        var forms = document.querySelectorAll('form[data-autosave="1"]');
        if (!forms.length) return;

        function cssEscape(value) {
            if (window.CSS && typeof window.CSS.escape === 'function') {
                return window.CSS.escape(value);
            }
            return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
        }

        forms.forEach(function (form) {
            var key = 'erpgo_draft_' + (window.location.pathname || '') + '_' + (form.getAttribute('action') || '');
            var indicatorId = 'autosaveIndicator';
            var indicator = document.getElementById(indicatorId);
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = indicatorId;
                indicator.style.position = 'fixed';
                indicator.style.bottom = '16px';
                indicator.style.right = '16px';
                indicator.style.zIndex = '99999';
                indicator.style.padding = '8px 10px';
                indicator.style.borderRadius = '10px';
                indicator.style.fontSize = '12px';
                indicator.style.background = 'rgba(0,0,0,0.75)';
                indicator.style.color = '#fff';
                indicator.style.display = 'none';
                document.body.appendChild(indicator);
            }

            function setIndicator(state) {
                var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
                var fr = lang.indexOf('fr') === 0;
                if (state === 'saving') {
                    indicator.textContent = fr ? 'Sauvegarde…' : 'Saving…';
                    indicator.style.display = '';
                    return;
                }
                if (state === 'saved') {
                    indicator.textContent = fr ? 'Enregistré' : 'Saved';
                    indicator.style.display = '';
                    setTimeout(function () { indicator.style.display = 'none'; }, 1000);
                    return;
                }
                if (state === 'error') {
                    indicator.textContent = fr ? 'Erreur de sauvegarde' : 'Save error';
                    indicator.style.display = '';
                }
            }

            var restored = false;
            var draft = readJsonStorage(key, null);
            if (draft && typeof draft === 'object') {
                var hasAnyValue = false;
                Object.keys(draft).forEach(function (name) {
                    var el = form.querySelector('[name="' + cssEscape(name) + '"]');
                    if (!el) return;
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = !!draft[name];
                        hasAnyValue = true;
                        return;
                    }
                    if (el.value == null || el.value === '') {
                        el.value = String(draft[name] == null ? '' : draft[name]);
                        hasAnyValue = true;
                    }
                });
                restored = hasAnyValue;
            }

            var debounce = null;
            function collectAndSave() {
                var data = {};
                form.querySelectorAll('input[name], select[name], textarea[name]').forEach(function (el) {
                    if (!el.name) return;
                    if (el.type === 'password' || el.type === 'file') return;
                    if (el.type === 'checkbox') {
                        data[el.name] = el.checked ? 1 : 0;
                        return;
                    }
                    if (el.type === 'radio') {
                        if (el.checked) data[el.name] = el.value;
                        return;
                    }
                    data[el.name] = el.value;
                });

                setIndicator('saving');
                var ok = writeJsonStorage(key, data);
                setIndicator(ok ? 'saved' : 'error');
            }

            form.addEventListener('input', function () {
                if (debounce) clearTimeout(debounce);
                debounce = setTimeout(collectAndSave, 400);
            }, { passive: true });
            form.addEventListener('change', function () {
                if (debounce) clearTimeout(debounce);
                debounce = setTimeout(collectAndSave, 200);
            }, { passive: true });

            form.addEventListener('submit', function () {
                try { localStorage.removeItem(key); } catch (e) { }
            });

            if (restored && window.show_toastr) {
                var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
                window.show_toastr('success', lang.indexOf('fr') === 0 ? 'Brouillon restauré' : 'Draft restored');
            }
        });
    }

            }
        });
    }

    // ==========================================
    // 11. PWA (Progressive Web App)
    // ==========================================
    function initPWA() {
        if (!('serviceWorker' in navigator)) return;
        
        // Register service worker
        navigator.serviceWorker.register('/sw.js')
            .then(function (registration) {
                console.log('Service Worker registered:', registration.scope);
                
                // Check for updates
                registration.addEventListener('updatefound', function () {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', function () {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateNotification();
                        }
                    });
                });
                
                // Request notification permission
                if ('Notification' in window && Notification.permission === 'default') {
                    requestNotificationPermission(registration);
                }
            })
            .catch(function (error) {
                console.log('Service Worker registration failed:', error);
            });
        
        // Show PWA install prompt
        setupInstallPrompt();
    }
    
    function requestNotificationPermission(registration) {
        setTimeout(function () {
            if (localStorage.getItem('pwa_notifications_asked')) return;
            
            var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
            var fr = lang.indexOf('fr') === 0;
            
            if (confirm(fr ? 'Activer les notifications push pour ERPGo ?' : 'Enable push notifications for ERPGo?')) {
                Notification.requestPermission().then(function (permission) {
                    if (permission === 'granted' && registration) {
                        registration.showNotification(fr ? 'Notifications activées' : 'Notifications enabled', {
                            body: fr ? 'Vous recevrez des alertes en temps réel' : 'You will receive real-time alerts',
                            icon: '/favicon.ico'
                        });
                    }
                });
            }
            localStorage.setItem('pwa_notifications_asked', 'true');
        }, 10000); // Ask after 10 seconds
    }
    
    function setupInstallPrompt() {
        var deferredPrompt;
        var installBtn = null;
        
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            showInstallButton();
        });
        
        function showInstallButton() {
            if (localStorage.getItem('pwa_install_dismissed')) return;
            if (document.getElementById('pwaInstallBtn')) return;
            
            var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
            var fr = lang.indexOf('fr') === 0;
            
            var btn = document.createElement('div');
            btn.id = 'pwaInstallBtn';
            btn.className = 'pwa-install-prompt show';
            btn.innerHTML = '<div class="pwa-install-prompt-header">' +
                '<span class="pwa-install-prompt-title">📱 ' + (fr ? 'Installer ERPGo' : 'Install ERPGo') + '</span>' +
                '<button class="pwa-install-prompt-close" onclick="dismissPWAInstall()">×</button></div>' +
                '<div class="pwa-install-prompt-body">' + 
                (fr ? 'Ajoutez à votre écran d\'accueil pour une expérience optimale' : 'Add to home screen for the best experience') + 
                '</div>' +
                '<div class="pwa-install-prompt-actions">' +
                '<button class="btn btn-primary btn-sm" id="confirmPwaInstall">' + 
                (fr ? 'Installer' : 'Install') + '</button></div>';
            
            document.body.appendChild(btn);
            
            document.getElementById('confirmPwaInstall').addEventListener('click', function () {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function (choiceResult) {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('PWA installed');
                        }
                        deferredPrompt = null;
                    });
                }
                btn.remove();
            });
        }
        
        window.dismissPWAInstall = function () {
            var btn = document.getElementById('pwaInstallBtn');
            if (btn) btn.remove();
            localStorage.setItem('pwa_install_dismissed', 'true');
        };
    }
    
    function showUpdateNotification() {
        var lang = (document.documentElement.getAttribute('lang') || '').toLowerCase();
        var fr = lang.indexOf('fr') === 0;
        
        if (window.show_toastr) {
            window.show_toastr('info', fr ? 'Nouvelle version disponible. Actualisez la page.' : 'New version available. Refresh the page.');
        }
    }

    // ==========================================
    // INIT ALL
    // ==========================================
    document.addEventListener('DOMContentLoaded', function () {
        initGlobalSearch();
        initNotificationCenter();
        initLocalRecentsTracking();
        initPrefetch();
        initRecentMenu();
        initSearchButton();
        initDarkMode();
        initNotifBell();
        initClockTimer();
        initFavoritesBar();
        initScrollToTop();
        initBatchActions();
        initKeyboardShortcuts();
        initFormAutosave();
        initPWA();
    });
})();
