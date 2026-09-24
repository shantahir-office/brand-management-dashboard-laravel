import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        // AI Studio Preview: Serve the Dashboard and handle JSON API for live testing
        {
            name: 'preview-serve-dashboard',
            configureServer(server) {
                server.middlewares.use((req, res, next) => {
                    // Serve Dashboard HTML on root preview
                    if (req.url === '/' || req.url === '/index.html') {
                        res.setHeader('Content-Type', 'text/html');
                        res.statusCode = 200;
                        res.end(fs.readFileSync('./index.html', 'utf8'));
                        return;
                    }

                    // Handle storage/app/brands.json operations in preview
                    if (req.url.startsWith('/api/brands') || req.url.startsWith('/brands')) {
                        const brandsFile = path.resolve(process.cwd(), 'storage/app/brands.json');
                        const readBrands = () => {
                            if (fs.existsSync(brandsFile)) {
                                try { return JSON.parse(fs.readFileSync(brandsFile, 'utf8')); }
                                catch { return { brands: [] }; }
                            }
                            return { brands: [] };
                        };
                        const writeBrands = (data) => {
                            fs.writeFileSync(brandsFile, JSON.stringify(data, null, 2), 'utf8');
                        };

                        if (req.method === 'GET' && !req.url.includes('/bulk')) {
                            res.setHeader('Content-Type', 'application/json');
                            res.end(JSON.stringify(readBrands()));
                            return;
                        }

                        // Bulk updates endpoint: /api/brands/bulk or /brands/bulk-update
                        if ((req.method === 'POST' || req.method === 'PUT') && (req.url.includes('/bulk') || req.url.includes('/bulk-update'))) {
                            let body = '';
                            req.on('data', chunk => { body += chunk; });
                            req.on('end', () => {
                                try {
                                    const parsed = JSON.parse(body);
                                    const data = readBrands();
                                    const targetIds = Array.isArray(parsed.ids) ? parsed.ids.map(Number) : [];
                                    let updatedCount = 0;

                                    data.brands = data.brands.map(brand => {
                                        if (targetIds.includes(Number(brand.id))) {
                                            updatedCount++;
                                            const updated = { ...brand };
                                            if (parsed.custom_field_name !== undefined) {
                                                updated.custom_field_name = parsed.custom_field_name ? String(parsed.custom_field_name).trim() : null;
                                            }
                                            if (parsed.custom_status !== undefined) {
                                                updated.custom_status = parsed.custom_status ? String(parsed.custom_status).trim() : null;
                                            }
                                            if (parsed.status !== undefined && parsed.status !== '') {
                                                updated.status = String(parsed.status).trim();
                                            }
                                            if (parsed.owner !== undefined && parsed.owner !== '') {
                                                updated.owner = String(parsed.owner).trim();
                                            }
                                            if (parsed.category !== undefined && parsed.category !== '') {
                                                updated.category = String(parsed.category).trim();
                                            }
                                            return updated;
                                        }
                                        return brand;
                                    });

                                    writeBrands(data);
                                    res.setHeader('Content-Type', 'application/json');
                                    res.end(JSON.stringify({ success: true, updatedCount, brands: data.brands }));
                                } catch (e) {
                                    res.statusCode = 500;
                                    res.end(JSON.stringify({ error: e.message }));
                                }
                            });
                            return;
                        }

                        if (req.method === 'POST') {
                            let body = '';
                            req.on('data', chunk => { body += chunk; });
                            req.on('end', () => {
                                try {
                                    const parsed = JSON.parse(body);
                                    const data = readBrands();
                                    const maxId = data.brands.reduce((max, b) => Math.max(max, Number(b.id) || 0), 0);
                                    const tech = (parsed.technology || (parsed.laravel ? 'Laravel' : 'PHP')).trim();
                                    const newBrand = {
                                        id: maxId + 1,
                                        category: (parsed.category || 'TM Brands').trim(),
                                        brand_name: (parsed.brand_name || 'Untitled Brand').trim(),
                                        technology: tech,
                                        repo_owner: parsed.repo_owner ? parsed.repo_owner.trim() : 'ahmedzafar-devTeam',
                                        token: parsed.token ? parsed.token.trim() : null,
                                        status: (parsed.status || 'Pending').trim(),
                                        owner: parsed.owner ? parsed.owner.trim() : null,
                                        repo_link: parsed.repo_link ? parsed.repo_link.trim() : null,
                                        laravel: tech === 'Laravel',
                                        ftp_details: parsed.ftp_details ? parsed.ftp_details.trim() : null,
                                        notes: parsed.notes ? parsed.notes.trim() : null,
                                        custom_field_name: parsed.custom_field_name ? parsed.custom_field_name.trim() : 'Number Update',
                                        custom_status: parsed.custom_status ? parsed.custom_status.trim() : null,
                                    };
                                    data.brands.push(newBrand);
                                    writeBrands(data);
                                    res.setHeader('Content-Type', 'application/json');
                                    res.end(JSON.stringify({ success: true, brand: newBrand }));
                                } catch (e) {
                                    res.statusCode = 500;
                                    res.end(JSON.stringify({ error: e.message }));
                                }
                            });
                            return;
                        }

                        if (req.method === 'PUT') {
                            let body = '';
                            req.on('data', chunk => { body += chunk; });
                            req.on('end', () => {
                                try {
                                    const parsed = JSON.parse(body);
                                    const data = readBrands();
                                    const idx = data.brands.findIndex(b => Number(b.id) === Number(parsed.id));
                                    if (idx !== -1) {
                                        const updatedTech = parsed.technology !== undefined
                                            ? (parsed.technology ? parsed.technology.trim() : 'Laravel')
                                            : (data.brands[idx].technology || (data.brands[idx].laravel ? 'Laravel' : 'PHP'));

                                        data.brands[idx] = {
                                            ...data.brands[idx],
                                            brand_name: (parsed.brand_name || data.brands[idx].brand_name).trim(),
                                            category: (parsed.category || data.brands[idx].category).trim(),
                                            technology: updatedTech,
                                            repo_owner: parsed.repo_owner !== undefined ? (parsed.repo_owner ? parsed.repo_owner.trim() : 'ahmedzafar-devTeam') : (data.brands[idx].repo_owner || 'ahmedzafar-devTeam'),
                                            token: parsed.token !== undefined ? (parsed.token ? parsed.token.trim() : null) : data.brands[idx].token,
                                            status: (parsed.status || data.brands[idx].status || 'Pending').trim(),
                                            owner: parsed.owner !== undefined ? (parsed.owner ? parsed.owner.trim() : null) : data.brands[idx].owner,
                                            repo_link: parsed.repo_link !== undefined ? (parsed.repo_link ? parsed.repo_link.trim() : null) : data.brands[idx].repo_link,
                                            laravel: updatedTech === 'Laravel',
                                            ftp_details: parsed.ftp_details !== undefined ? (parsed.ftp_details ? parsed.ftp_details.trim() : null) : data.brands[idx].ftp_details,
                                            notes: parsed.notes !== undefined ? (parsed.notes ? parsed.notes.trim() : null) : data.brands[idx].notes,
                                            custom_field_name: parsed.custom_field_name !== undefined ? (parsed.custom_field_name ? parsed.custom_field_name.trim() : null) : (data.brands[idx].custom_field_name || 'Number Update'),
                                            custom_status: parsed.custom_status !== undefined ? (parsed.custom_status ? parsed.custom_status.trim() : null) : (data.brands[idx].custom_status || null),
                                        };
                                        writeBrands(data);
                                        res.setHeader('Content-Type', 'application/json');
                                        res.end(JSON.stringify({ success: true, brand: data.brands[idx] }));
                                    } else {
                                        res.statusCode = 404;
                                        res.end(JSON.stringify({ error: 'Not found' }));
                                    }
                                } catch (e) {
                                    res.statusCode = 500;
                                    res.end(JSON.stringify({ error: e.message }));
                                }
                            });
                            return;
                        }

                        if (req.method === 'DELETE') {
                            let body = '';
                            req.on('data', chunk => { body += chunk; });
                            req.on('end', () => {
                                try {
                                    const parsed = JSON.parse(body);
                                    const data = readBrands();
                                    data.brands = data.brands.filter(b => Number(b.id) !== Number(parsed.id));
                                    writeBrands(data);
                                    res.setHeader('Content-Type', 'application/json');
                                    res.end(JSON.stringify({ success: true }));
                                } catch (e) {
                                    res.statusCode = 500;
                                    res.end(JSON.stringify({ error: e.message }));
                                }
                            });
                            return;
                        }
                    }

                    next();
                });
            },
        },
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
server: {
    host: '0.0.0.0',
    port: 3001,
    cors: true,
    origin: 'http://localhost:3001',
},
});
