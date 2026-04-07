import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Dashboard() {
    const { auth } = usePage().props;
    const user = auth.user;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        <div className="rounded-lg bg-white p-6 shadow-sm">
                            <h3 className="text-lg font-semibold text-gray-900">Browse Boards</h3>
                            <p className="mt-2 text-sm text-gray-600">
                                Explore public notice boards, join communities, and view approved notices.
                            </p>
                            <Link
                                href={route('boards.index')}
                                className="mt-4 inline-block rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            >
                                View Boards
                            </Link>
                        </div>

                        <div className="rounded-lg bg-white p-6 shadow-sm">
                            <h3 className="text-lg font-semibold text-gray-900">Create a Board</h3>
                            <p className="mt-2 text-sm text-gray-600">
                                Set up a new public or private board and automatically become its first admin member.
                            </p>
                            <Link
                                href={route('boards.create')}
                                className="mt-4 inline-block rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                            >
                                Create Board
                            </Link>
                        </div>

                        {user?.role === 'admin' && (
                            <div className="rounded-lg bg-white p-6 shadow-sm">
                                <h3 className="text-lg font-semibold text-gray-900">Admin Queue</h3>
                                <p className="mt-2 text-sm text-gray-600">
                                    Review pending submissions and approve, reject, or delete them.
                                </p>
                                <Link
                                    href={route('admin.index')}
                                    className="mt-4 inline-block rounded bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700"
                                >
                                    Open Admin
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
