import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function MyBoards({ boards }) {
    const { auth } = usePage().props;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    My Boards
                </h2>
            }
        >
            <Head title="My Boards" />

            <div className="py-12">
                <div className="mx-auto max-w-5xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 className="mb-6 text-lg font-semibold text-gray-900">Boards You Belong To</h3>

                        {boards.length === 0 ? (
                            <div className="rounded-lg border border-dashed border-gray-300 p-6 text-gray-600">
                                You have not joined any boards yet.
                            </div>
                        ) : (
                            <div className="grid gap-4 sm:grid-cols-2">
                                {boards.map((board) => (
                                    <div
                                        key={board.id}
                                        className="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                                    >
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <h4 className="text-lg font-semibold text-gray-900">
                                                    {board.name}
                                                </h4>
                                                <p className="mt-1 text-sm text-gray-500">
                                                    Created by {board.owner?.name ?? 'Unknown'}
                                                </p>
                                            </div>

                                            <span
                                                className={
                                                    'rounded-full px-3 py-1 text-xs font-semibold ' +
                                                    (board.is_private
                                                        ? 'bg-purple-100 text-purple-700'
                                                        : 'bg-blue-100 text-blue-700')
                                                }
                                            >
                                                {board.is_private ? 'Private' : 'Public'}
                                            </span>
                                        </div>

                                        <p className="mt-4 text-sm text-gray-700">
                                            {board.description || 'No description yet.'}
                                        </p>

                                        <div className="mt-4 flex flex-wrap gap-2">
                                            {board.member_role === 'admin' ? (
                                                <span className="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                    Board Admin
                                                </span>
                                            ) : (
                                                <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                    Member
                                                </span>
                                            )}

                                            <span className="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                {board.member_count} member{board.member_count === 1 ? '' : 's'}
                                            </span>
                                        </div>

                                        <div className="mt-5">
                                            <Link
                                                href={route('boards.show', board.id)}
                                                className="inline-block rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                            >
                                                Open Board
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}