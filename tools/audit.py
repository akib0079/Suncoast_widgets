#!/usr/bin/env python3
"""
Pre-build checks for the Suncoast widget plugin.

Three classes of mistake this project has actually hit:

  1. A root/section class (.sce-faq, .sce-cta, …) sits on the SAME element as
     .sce-scope, so `.sce-scope .sce-faq__x` is fine but `.sce-scope .sce-faq`
     or `.sce-scope .sce-faq--modifier` can never match. Rules written that way
     are silently dead — a control that appears to do nothing.
  2. A rule that does not start from .sce-scope loses the theme-proofing.
  3. A setting read in render() that no control declares, so it is always ''.

Run from anywhere:  python3 tools/audit.py
"""
import re, sys, glob, os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CSS = os.path.join(ROOT, 'plugin/suncoast-ele-widgets/assets/css')
PHP = os.path.join(ROOT, 'plugin/suncoast-ele-widgets/includes/widgets')

# `!important` is allowed only in the prefers-reduced-motion kill switch
MOTION_KILL = ('animation-duration', 'animation-iteration-count',
               'transition-duration', 'scroll-behavior')


def strip_comments(s):
    return re.sub(r'/\*.*?\*/', '', s, flags=re.S)


def selectors(src):
    """Yield (selector_part, raw_selector) for real rules only.

    Keyframe stops and the inside of :where()/:is() lists are not selectors,
    so both are removed before splitting on commas.
    """
    src = re.sub(r'@keyframes[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}', '', src, flags=re.S)
    for raw in re.findall(r'(?m)^([^{}@\n][^{}]*?)\{', src):
        flat = re.sub(r'\([^()]*\)', '()', raw)          # collapse (…) lists
        for part in (p.strip() for p in flat.split(',')):
            if part:
                yield part, raw


def audit_css():
    bad = []
    roots = {'sce-scope'}
    for f in glob.glob(os.path.join(CSS, '*.css')):
        src = open(f, encoding='utf-8').read()
        roots |= {m.split('--')[0] for m in
                  re.findall(r'(?m)^\.sce-scope\.(sce-[a-z0-9_-]+)\s*\{', src)}

    for f in sorted(glob.glob(os.path.join(CSS, '*.css'))):
        name = os.path.basename(f)
        raw_src = open(f, encoding='utf-8').read()
        src = strip_comments(raw_src)

        if src.count('{') != src.count('}'):
            bad.append((name, 'BRACES', 'unbalanced'))

        for i, line in enumerate(strip_comments(raw_src).split('\n'), 1):
            if '!important' in line and not any(k in line for k in MOTION_KILL):
                bad.append((name, 'IMPORTANT', 'line %d: %s' % (i, line.strip())))

        for part, _ in selectors(src):
            for m in re.finditer(r'\.sce-scope\s+\.(sce-[a-z0-9_-]+)', part):
                if m.group(1).split('--')[0] in roots:
                    bad.append((name, 'DEAD SELECTOR', part))
            if '.sce-' not in part:
                bad.append((name, 'UNSCOPED', part))
    return bad


def audit_php():
    bad = []
    for f in sorted(glob.glob(os.path.join(PHP, '*.php'))):
        name = os.path.basename(f)
        src = open(f, encoding='utf-8').read()
        declared = set(re.findall(r"add(?:_responsive)?_control\(\s*\n?\s*'([a-z0-9_]+)'", src))
        declared |= set(re.findall(r"add_group_control\([^;]*?'name'\s*=>\s*'([a-z0-9_]+)'", src, re.S))
        # controls whose key is built from a loop variable
        dyn = re.findall(r"add_control\(\s*\n?\s*'([a-z0-9_]+_)'\s*\.\s*\$\w+", src)
        for key in set(re.findall(r"\$s\[\s*'([a-z0-9_]+)'\s*\]", src)) - declared:
            if not any(key.startswith(p) for p in dyn):
                bad.append((name, 'UNDECLARED SETTING', key))
    return bad


if __name__ == '__main__':
    problems = audit_css() + audit_php()
    if problems:
        print('AUDIT FAILED (%d)\n' % len(problems))
        for p in problems:
            print('  %-20s %-20s %s' % p)
        sys.exit(1)
    print('audit: css selectors scoped and live, widget settings all declared')
