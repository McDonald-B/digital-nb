import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Create() {
    const { auth } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        category: 'General',
        is_private: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('boards.store'));
    };

    const categories = [
        'General',
        'Sports',
        'University',
        'Events',
        'Housing',
        'Jobs',
        'Society',
        'Marketplace',
    ];

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create Board
                </h2>
            }
        >
            <Head title="Create Board" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <label className="mb-1 block text-sm font-medium text-gray-700">
                                    Board Name
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full rounded border border-gray-300 px-3 py-2"
                                />
                                {errors.name && <div className="mt-1 text-sm text-red-600">{errors.name}</div>}
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows="4"
                                    className="w-full rounded border border-gray-300 px-3 py-2"
                                />
                                {errors.description && (
                                    <div className="mt-1 text-sm text-red-600">{errors.description}</div>
                                )}
                            </div>

                            <div>
                                <label className="mb-1 block text-sm font-medium text-gray-700">
                                    Category
                                </label>
                                <select
                                    value={data.category}
                                    onChange={(e) => setData('category', e.target.value)}
                                    className="w-full rounded border border-gray-300 px-3 py-2"
                                >
                                    {categories.map((category) => (
                                        <option key={category} value={category}>
                                            {category}
                                        </option>
                                    ))}
                                </select>
                                {errors.category && (
                                    <div className="mt-1 text-sm text-red-600">{errors.category}</div>
                                )}
                            </div>

                            <div className="flex items-center gap-3">
                                <input
                                    id="is_private"
                                    type="checkbox"
                                    checked={data.is_private}
                                    onChange={(e) => setData('is_private', e.target.checked)}
                                    className="rounded border-gray-300"
                                />
                                <label htmlFor="is_private" className="text-sm text-gray-700">
                                    Make this board private
                                </label>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Create Board
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}