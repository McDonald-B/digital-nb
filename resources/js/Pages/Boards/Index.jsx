import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Index({ boards, filters, categories }) {
    const { auth } = usePage().props;

    const updateFilter = (key, value) => {
        router.get(
            route('boards.index'),
            {
                ...filters,
                [key]: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Boards
                </h2>
            }
        >
            <Head title="Boards" />

            <div className="py-12">
                <div className="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                            <div className="grid flex-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700">
                                        Search
                                    </label>
                                    <input
                                        type="text"
                                        value={filters.search}
                                        onChange={(e) => updateFilter('search', e.target.value)}
                                        placeholder="Search boards by name"
                                        className="w-full rounded border border-gray-300 px-3 py-2"
                                    />
                                </div>

                                <div>
                                    <label className="mb-1 block text-sm font-medium text-gray-700">
                                        Category
                                    </label>
                                    <select
                                        value={filters.category}
                                        onChange={(e) => updateFilter('category', e.target.value)}
                                        className="w-full rounded border border-gray-300 px-3 py-2"
                                    >
                                        <option value="">All Categories</option>
                                        {categories.map((category) => (
                                            <option key={category} value={category}>
                                                {category}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="flex gap-3">
                                <Link
                                    href={route('boards.recommended')}
                                    className="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                                >
                                    Recommended Boards
                                </Link>

                                <Link
                                    href={route('boards.create')}
                                    className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                                >
                                    Create Board
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        {boards.length === 0 ? (
                            <div className="rounded-lg bg-white p-6 text-gray-600 shadow-sm">
                                No boards found.
                            </div>
                        ) : (
                            boards.map((board) => (
                                <div
                                    key={board.id}
                                    className="rounded-lg bg-white p-6 shadow-sm"
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">{board.name}</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                Created by {board.owner?.name ?? 'Unknown'}
                                            </p>
                                        </div>

                                        <span className="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {board.category}
                                        </span>
                                    </div>

                                    <p className="mt-4 text-sm text-gray-700">
                                        {board.description || 'No description yet.'}
                                    </p>

                                    <div className="mt-4 flex flex-wrap gap-2">
                                        <span className="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {board.member_count} member{board.member_count === 1 ? '' : 's'}
                                        </span>

                                        <span
                                            className={
                                                'rounded-full px-3 py-1 text-xs font-semibold ' +
                                                (board.is_private
                                                    ? 'bg-purple-100 text-purple-700'
                                                    : 'bg-green-100 text-green-700')
                                            }
                                        >
                                            {board.is_private ? 'Private' : 'Public'}
                                        </span>
                                    </div>

                                    <div className="mt-5">
                                        <Link
                                            href={route('boards.show', board.id)}
                                            className="inline-block rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                        >
                                            View Board
                                        </Link>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}