import { Link } from '@inertiajs/react';

export default function MainLayout({ children }) {
  return (
    <div className="min-h-screen bg-gray-50">
      <nav className="bg-blue-900 text-white px-6 py-4 flex justify-between items-center">
        <Link href="/boards" className="text-xl font-bold">Digital Notice Board</Link>
        <div className="flex gap-4">
          <Link href="/dashboard" className="hover:underline">My Boards</Link>
          <Link href="/boards" className="hover:underline">Browse</Link>
          <Link href="/logout" method="post" as="button" className="hover:underline">Logout</Link>
        </div>
      </nav>
      <main className="container mx-auto px-4 py-8">{children}</main>
    </div>
  );
}
