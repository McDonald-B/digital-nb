import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useState } from 'react';

export default function Index({ boards, filters }) {
    const [search, setSearch] = useState(filters.search || '');

    const submitSearch = (e) => {
        e.preventDefault();

        router.get(
            route('boards.index'),
            { search },
            {
                preserveState: true,
                replace: true,
            }
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Notice Boards
                </h2>
            }
        >
            <Head title="Boards" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div className="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <form onSubmit={submitSearch} className="flex flex-col gap-2 sm:flex-row">
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Search boards..."
                                    className="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <button
                                    type="submit"
                                    className="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                                >
                                    Search
                                </button>
                            </form>

                            <Link
                                href={route('boards.create')}
                                className="rounded bg-green-600 px-4 py-2 text-center text-white hover:bg-green-700"
                            >
                                Create Board
                            </Link>
                        </div>

                        {boards.length === 0 ? (
                            <p className="text-gray-600">No boards found.</p>
                        ) : (
                            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                {boards.map((board) => (
                                    <Link
                                        key={board.id}
                                        href={route('boards.show', board.id)}
                                        className="rounded-lg border border-gray-200 p-5 transition hover:border-indigo-300 hover:bg-gray-50"
                                    >
                                        <div className="flex items-start justify-between gap-3">
                                            <h3 className="text-lg font-semibold text-gray-900">{board.name}</h3>

                                            <span
                                                className={
                                                    'rounded px-2 py-1 text-xs font-medium ' +
                                                    (board.is_private
                                                        ? 'bg-red-100 text-red-700'
                                                        : 'bg-blue-100 text-blue-700')
                                                }
                                            >
                                                {board.is_private ? 'Private' : 'Public'}
                                            </span>
                                        </div>

                                        <p className="mt-3 text-sm text-gray-600">
                                            {board.description || 'No description yet.'}
                                        </p>

                                        <div className="mt-4 flex items-center justify-between text-xs text-gray-500">
                                            <span>Created by {board.owner?.name ?? 'Unknown'}</span>
                                            <span>{board.is_member ? 'Joined' : 'Not joined'}</span>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
