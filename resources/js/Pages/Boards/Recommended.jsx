import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Recommended({ recommendedBoards, joinedCategories }) {
    const { auth } = usePage().props;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Recommended Boards
                </h2>
            }
        >
            <Head title="Recommended Boards" />

            <div className="py-12">
                <div className="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 className="text-lg font-semibold text-gray-900">Why these are recommended</h3>

                        {joinedCategories.length === 0 ? (
                            <p className="mt-2 text-gray-600">
                                Join some boards first so we can recommend similar ones by category.
                            </p>
                        ) : (
                            <div className="mt-4 flex flex-wrap gap-2">
                                {joinedCategories.map((category) => (
                                    <span
                                        key={category}
                                        className="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700"
                                    >
                                        {category}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        {recommendedBoards.length === 0 ? (
                            <div className="rounded-lg bg-white p-6 text-gray-600 shadow-sm">
                                No recommended boards yet.
                            </div>
                        ) : (
                            recommendedBoards.map((board) => (
                                <div key={board.id} className="rounded-lg bg-white p-6 shadow-sm">
                                    <div className="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 className="text-lg font-semibold text-gray-900">{board.name}</h3>
                                            <p className="mt-1 text-sm text-gray-500">
                                                Created by {board.owner?.name ?? 'Unknown'}
                                            </p>
                                        </div>

                                        <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            {board.category}
                                        </span>
                                    </div>

                                    <p className="mt-4 text-sm text-gray-700">
                                        {board.description || 'No description yet.'}
                                    </p>

                                    <div className="mt-4">
                                        <span className="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                            {board.member_count} member{board.member_count === 1 ? '' : 's'}
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