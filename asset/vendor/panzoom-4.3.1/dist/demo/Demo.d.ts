import React from 'react';
interface Props {
    title: string;
    subtitle?: string;
    code: React.ReactNode;
    children: React.ReactNode;
}
export default function Demo({ title, subtitle, code, children }: Props): JSX.Element;
export {};
